<?php


namespace Fulfillment\TriagedQueues\Queue;


use Fulfillment\TriagedQueues\Queue\Jobs\BetterSyncJob;
use Illuminate\Queue\SyncQueue;

class BetterSyncQueue extends SyncQueue
{
	/**
	 * Resolve a Sync job instance.
	 *
	 * @param  string      $payload
	 * @param  string|null $queue
	 *
	 * @return \Illuminate\Contracts\Queue\Job
	 */
	protected function resolveJob($payload, $queue)
	{
		return new BetterSyncJob($this->container, $payload, $queue);
	}
}