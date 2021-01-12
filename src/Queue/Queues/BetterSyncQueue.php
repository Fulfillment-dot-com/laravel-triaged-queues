<?php


namespace Fulfillment\TriagedQueues\Queue\Queues;


use Fulfillment\TriagedQueues\Queue\Jobs\BetterSyncJob;
use Illuminate\Queue\SyncQueue;

class BetterSyncQueue extends SyncQueue
{
	use BetterQueue;

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
		return new BetterSyncJob($this->container, $payload, $this->connectionName, $queue);
	}

	protected function createPayload($job, $data = '', $queue = null)
	{
		// if no custom job then just use laravel function
		// probably will be better for forward compatibility
		if($this->getCustomPayloadJob() === null || !is_object($job))
		{
			return parent::createPayload($job, $data, $queue);
		}

		return json_encode([
			'job' => $this->getCustomPayloadJob(),
			'data' => ['command' => serialize(clone $job)],
		]);
	}
}