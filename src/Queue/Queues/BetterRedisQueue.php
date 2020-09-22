<?php


namespace Fulfillment\TriagedQueues\Queue\Queues;


use Fulfillment\TriagedQueues\Queue\Jobs\BetterSyncJob;
use Illuminate\Queue\RedisQueue;
use Illuminate\Queue\SyncQueue;

class BetterRedisQueue extends RedisQueue
{
	use BetterQueue;

	protected function createPayload($job, $data = '', $queue = null)
	{
		// if no custom job then just use laravel function
		// probably will be better for forward compatibility
		if ($this->getCustomPayloadJob() === null || !is_object($job))
		{
			return parent::createPayload($job, $data, $queue);
		}
		$payload = json_encode([
			'job'  => $this->getCustomPayloadJob(),
			'data' => ['command' => serialize(clone $job)],
		]);

		$payload = $this->setMeta($payload, 'id', $this->getRandomId());

		return $this->setMeta($payload, 'attempts', 1);
	}
}