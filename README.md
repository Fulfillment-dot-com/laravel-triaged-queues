# Laravel 5 Triaged Queues

*Extend Laravel 5 Queues to fallback to an arbitrary number of hosts in the event one is unreachable.*

This package extends Laravel 5 Queues by adding these features:

* Enable connection attempts to multiple hosts, sequentially, in the event the primary host is unreachable.
* If all hosts for a connection fail, enable falling back to the `sync` driver so that a job/command can be processed synchronously and therefore avoid data loss.

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
    ],
```

TriagedQueues offers two extra entries:

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

## Contributing

Contributing additional drivers is welcomed! The steps for creating a new driver are simple:

1. Create a new class in `Fulfillment\TriagedQueues\Queue\Connectors` that implements `Illuminate\Queue\Connectors\ConnectorInterface`
2. Implement `ConnectorInterface` (the `connect()` function), make sure your method attempts all listed hosts in the `$config` parameter.
3. If no host attempt works, throw `NoHostException`

Then make a PR and I will happily accept it :)

## License

This package is licensed under the [MIT license](https://github.com/Fulfillment-dot-com/laravel-triaged-queues/blob/master/LICENSE.txt).