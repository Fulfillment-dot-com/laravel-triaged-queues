<?php


namespace Fulfillment\TriagedQueues\Queue;
use Fulfillment\TriagedQueues\Exceptions\NoHostException;
use Log;

class QueueManager extends \Illuminate\Queue\QueueManager
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
                    $name = 'sync';

                    $this->connections['sync'] = $this->resolve('sync');
                } else {
                    throw $e;
                }
            }

            $this->connections[$name]->setContainer($this->app);
        }

        return $this->connections[$name];
    }
}