<?php


namespace Fulfillment\TriagedQueues;


use Fulfillment\TriagedQueues\Queue\Connectors\BeanstalkdConnector;
use Fulfillment\TriagedQueues\Queue\QueueManager;
use Illuminate\Queue\QueueServiceProvider;

class TriagedQueueServiceProvider extends QueueServiceProvider
{
    protected function registerManager()
    {
	    $this->app->singleton('queue', function ($app) {
		    return tap(new QueueManager($app), function ($manager) {
			    $this->registerConnectors($manager);
		    });
	    });
    }

    protected function registerBeanstalkdConnector($manager)
    {
        $manager->addConnector('beanstalkd', function () {
            return new BeanstalkdConnector();
        });
    }
}