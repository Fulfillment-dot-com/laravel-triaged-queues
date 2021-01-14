<?php


namespace Fulfillment\TriagedQueues\Queue;
use Fulfillment\TriagedQueues\Exceptions\NoHostException;
use Illuminate\Queue\QueueManager as IlluminateQueueManager;
use Log;

class QueueManager extends IlluminateQueueManager
{
    public function connection($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        if (!isset($this->connections[$name])) {
            try {
                $this->connections[$name] = $this->resolve($name);
            } catch (NoHostException $e) {
                $config = $this->getConfig($name);
                // check to see if config wants to use sync as fallback
                if (isset($config['fallbackToSync']) && $config['fallbackToSync']) {
                    Log::warning("No working host found for driver $name. Falling back to sync driver.");

                    $this->connections[$name] = $this->resolve('sync');
                } else {
                    throw $e;
                }
            }

            $this->connections[$name]->setContainer($this->app);
        }

        return $this->connections[$name];
    }
}