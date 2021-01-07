<?php


namespace Fulfillment\TriagedQueues\Queue\Queues;


use Fulfillment\TriagedQueues\Queue\Jobs\BetterBeanstalkdJob;
use Illuminate\Queue\BeanstalkdQueue;
use Pheanstalk\Job as PheanstalkJob;

class BetterBeanstalkdQueue extends BeanstalkdQueue
{
	use BetterQueue;
	/**
	 * Pop the next job off of the queue.
	 *
	 * @param  string  $queue
	 * @return \Illuminate\Contracts\Queue\Job|null
	 */
	public function pop($queue = null)
	{
		$queue = $this->getQueue($queue);

		$job = $this->pheanstalk->watchOnly($queue)->reserve(0);

		if ($job instanceof PheanstalkJob) {
			return new BetterBeanstalkdJob($this->container, $this->pheanstalk, $job, $this->connectionName, $queue);
		}
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