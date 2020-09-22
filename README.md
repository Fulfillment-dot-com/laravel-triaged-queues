# Laravel 5.1 Triaged Queues

*Extend Laravel 5.1 Queues to fallback to an arbitrary number of hosts in the event one is unreachable.*

This package extends Laravel 5.1 Queues by adding these features:

* Enable connection attempts to multiple hosts, sequentially, in the event the primary host is unreachable.
* If all hosts for a connection fail, enable falling back to the `sync` driver so that a job/command can be processed synchronously and therefore avoid data loss.
* (Beanstalkd Only) An extra `touch()` pheanstalk command is available on jobs and socket timeout can be specified via queue config.

## Installation

Require this package  

```php
composer require "fulfillment/laravel-triaged-queues:0.1.*"
```

After adding the package, add the ServiceProvider to the providers array in `config/app.php`

```php
Fulfillment\TriagedQueues\TriagedQueueServiceProvider::class,
```

and remove Laravel's `QueueServiceProvider` if present in  `config/app.php`

```php
'Illuminate\Queue\QueueSeverProvider'
```

## Supported Drivers

Currently only the `beanstalkd` driver is supported. PRs for additional drivers are welcome.

## Usage

A normal Queue Connection from `config/queue.php` looks like this:

```php
'beanstalkd' => [
    'driver'         => 'beanstalkd',
    'host'           => env('BEANSTALK_HOST', 'localhost'),
    'queue'          => 'default',
    'ttr'            => 60,
    'socketTimeout'  => null
    ],
```

TriagedQueues offers three entries:

### hostN

Simply add an arbitrary number of host entries to the connection with this syntax

```php
'host[N]' => '[host value]'
```

where **[N]** is the order you want that host to be attempted in. The primary host does not need a number.

EX:

```php
'host'  => 'server.domain.com',
'host1' => 'server-fallback1.domain.com',
'host2' => 'server-fallback2.domain.com',
...
```

### fallbackToSync

If the entry `fallbackToSync` is set and `true` then TriagedQueues will use the `sync` driver in the event all hosts are unreachable.

EX:

```php
'host'           => 'server.domain.com',
'host1'          => 'server-fallback1.domain.com',
'host2'          => 'server-fallback2.domain.com',
'fallbackToSync' => true
...
```

### attempts

Adding an `attempts` entry will make TriagedQueues try to establish a connection X number of tries before moving to the next host.

```php
'attempts' => 2
```

### touch() (Beanstalkd Only)

A modified version of the `BeanstalkdJob` includes a `touch()` method which will send a `touch` command for the job to the beanstalkd queue. This resets the time-left (TTR minus running time) on the job so it isn't kicked back to the ready queue.

### socketTimeout (Beanstalkd Only)

If a job is long-running one must either increase TTR (time-to-run) for the job or `touch` it periodically to keep it reserved. However there is another factor that determines job behavior: [if the client disconnects while a job is reserved the job will be kicked back to the ready queue regardless of TTR](https://github.com/kr/beanstalkd/issues/11#issue-29600).

So for jobs that run longer than the default socket timeout (60 seconds, in ini settings) one must `touch` the job periodically, change this ini setting, or use the `socketTimeout` key-value in the configuration to specify timeout manually for `fsockopen`.

### Custom Queued Job

The default class that handles the "firing" of the command you have dispatched from the queued is `Illuminate\Queue\CallQueuedHandler`. However this default functionality does not allow a user to setup any logic for before the command is fired.

Using the `job` property in each Queue Connection you may specify an arbitrary `class@method` that will be called instead of `CallQueuedHandler` **when using automatic payload creation.** This means this only works if you use `Bus::dispatch()` or a similar method where the dispatcher creates the payload for you (and it is not a closure or raw payload).
 
Config looks like this:

```php
'beanstalkd' => [
    'driver'         => 'beanstalkd',
    'host'           => env('BEANSTALK_HOST', 'localhost'),
    'queue'          => 'default',
    'ttr'            => 60,
    'socketTimeout'  => null,
    'job'            => 'app\MyCustomHandler@call'
    ],
```

I suggest you `extend` from `CallQueuedHandler` and override `call` to make things easier.

An example of using this could be to set authentication for the laravel worker instance before the job is handled.

```php
class CallAuthenticatableQueuedHandler extends CallQueuedHandler
{
	public function call(Job $job, array $data)
	{
		$command = $this->setJobInstanceIfNecessary(
			$job, unserialize($data['command'])
		);
  
                // pipe an authentication middleware before the command is fired
		$this->dispatcher->pipeThrough([AuthenticateDispatchedJob::class])->dispatchNow($command, function ($handler) use ($job) {
			$this->setJobInstanceIfNecessary($job, $handler);
		});

		if (! $job->isDeletedOrReleased()) {
			$job->delete();
		}
	}
}
```


## Contributing

Contributing additional drivers is welcomed! The steps for creating a new driver are simple:

1. Create a new class in `Fulfillment\TriagedQueues\Queue\Connectors` that implements `Illuminate\Queue\Connectors\ConnectorInterface`
2. Implement `ConnectorInterface` (the `connect()` function), make sure your method attempts all listed hosts in the `$config` parameter.
3. If no host attempt works, throw `NoHostException`

Then make a PR and I will happily accept it :)

## License

This package is licensed under the [MIT license](https://github.com/Fulfillment-dot-com/laravel-triaged-queues/blob/master/LICENSE.txt).