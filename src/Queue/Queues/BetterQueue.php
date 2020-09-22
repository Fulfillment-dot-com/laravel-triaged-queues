<?php


namespace Fulfillment\TriagedQueues\Queue\Queues;

trait BetterQueue
{
	protected $customPayloadJob;

	public function setCustomPayloadJob($jobString)
	{
		$this->customPayloadJob = $jobString;
	}

	public function getCustomPayloadJob()
	{
		return $this->customPayloadJob;
	}
}