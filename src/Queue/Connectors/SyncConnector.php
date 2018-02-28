<?php

namespace Fulfillment\TriagedQueues\Queue\Connectors;

use Fulfillment\TriagedQueues\Queue\BetterSyncQueue;

class SyncConnector extends \Illuminate\Queue\Connectors\SyncConnector
{
	/**
	 * Establish a queue connection.
	 *
	 * @param  array  $config
	 * @return \Illuminate\Contracts\Queue\Queue
	 */
	public function connect(array $config)
	{
		return new BetterSyncQueue;
	}
}