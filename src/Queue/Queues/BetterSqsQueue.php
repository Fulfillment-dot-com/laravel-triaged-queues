<?php


namespace Fulfillment\TriagedQueues\Queue\Queues;


use Fulfillment\TriagedQueues\Queue\Jobs\BetterSyncJob;
use Illuminate\Queue\RedisQueue;
use Illuminate\Queue\SqsQueue;
use Illuminate\Queue\SyncQueue;

class BetterSqsQueue extends SqsQueue
{
	use BetterQueue;

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