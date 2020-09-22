<?php

namespace Fulfillment\TriagedQueues\Queue\Connectors;

use Fulfillment\TriagedQueues\Queue\Queues\BetterRedisQueue;
use Illuminate\Support\Arr;

class RedisConnector extends \Illuminate\Queue\Connectors\RedisConnector
{
	/**
	 * Establish a queue connection.
	 *
	 * @param  array  $config
	 * @return \Illuminate\Contracts\Queue\Queue
	 */
	public function connect(array $config)
	{
		$queue = new BetterRedisQueue(
			$this->redis, $config['queue'], Arr::get($config, 'connection', $this->connection)
		);

		if(null !== $job = Arr::get($config, 'job')) {
			$queue->setCustomPayloadJob($job);
		}

		$queue->setExpire(Arr::get($config, 'expire', 60));

		return $queue;
	}
}