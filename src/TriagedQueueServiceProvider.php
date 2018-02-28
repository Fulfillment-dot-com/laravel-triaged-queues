<?php


namespace Fulfillment\TriagedQueues;


use Fulfillment\TriagedQueues\Queue\Connectors\BeanstalkdConnector;
use Fulfillment\TriagedQueues\Queue\Connectors\SyncConnector;
use Fulfillment\TriagedQueues\Queue\QueueManager;
use Illuminate\Queue\QueueServiceProvider;

class TriagedQueueServiceProvider extends QueueServiceProvider
{
    protected function registerManager()
    {
        $this->app->singleton('queue', function ($app) {

            $manager = new QueueManager($app);

            $this->registerConnectors($manager);

            return $manager;
        });

        $this->app->singleton('queue.connection', function ($app) {
            return $app['queue']->connection();
        });
    }

    protected function registerBeanstalkdConnector($manager)
    {
        $manager->addConnector('beanstalkd', function () {
            return new BeanstalkdConnector();
        });
    }

	protected function registerSyncConnector($manager)
	{
		$manager->addConnector('sync', function () {
			return new SyncConnector;
		});
	}
}