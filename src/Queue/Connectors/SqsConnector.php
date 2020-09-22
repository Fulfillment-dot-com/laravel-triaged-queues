<?php

namespace Fulfillment\TriagedQueues\Queue\Connectors;

use Fulfillment\TriagedQueues\Queue\Queues\BetterSqsQueue;
use Illuminate\Support\Arr;

class SqsConnector extends \Illuminate\Queue\Connectors\SqsConnector
{
	/**
	 * Establish a queue connection.
	 *
	 * @param  array  $config
	 * @return \Illuminate\Contracts\Queue\Queue
	 */
	public function connect(array $config)
	{
		$config = $this->getDefaultConfiguration($config);

		if ($config['key'] && $config['secret']) {
			$config['credentials'] = Arr::only($config, ['key', 'secret']);
		}

		$queue = new BetterSqsQueue(
			new SqsClient($config), $config['queue'], Arr::get($config, 'prefix', '')
		);
		if(null !== $job = Arr::get($config, 'job')) {
			$queue->setCustomPayloadJob($job);
		}
		return $queue;
	}
}