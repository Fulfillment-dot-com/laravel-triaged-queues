<?php

namespace Fulfillment\TriagedQueues\Queue\Connectors;

use Fulfillment\TriagedQueues\Exceptions\NoHostException;
use Illuminate\Queue\BeanstalkdQueue;
use Log;

class BeanstalkdConnector extends \Illuminate\Queue\Connectors\BeanstalkdConnector
{
    /**
     * Establish a queue connection.
     *
     * @param  array $config
     * @return \Illuminate\Contracts\Queue\Queue
     * @throws NoHostException
     */
    public function connect(array $config)
    {
        $maxAttempts = isset($config['attempts']) ? $config['attempts'] : 1;
	    $staggerAttempts = isset($config['staggerAttempts']) ? $config['staggerAttempts'] : 0;

	    /** @var BeanstalkdQueue $queue */
	    $queue    = parent::connect($config);
	    $attempts = 0;
	    while($attempts < $maxAttempts) {
		    if($queue->getPheanstalk()->getConnection()->isServiceListening()) { // found a working host
			    return $queue;
		    }
		    sleep($staggerAttempts);
		    $attempts++;
	    }

	    throw new NoHostException('Beanstalk host was unreachable');
    }
}