<?php


namespace Fulfillment\TriagedQueues\Queue;


use Fulfillment\TriagedQueues\Queue\Jobs\BetterBeanstalkdJob;
use Illuminate\Queue\BeanstalkdQueue;
use Pheanstalk\Job as PheanstalkJob;

class BetterBeanstalkdQueue extends BeanstalkdQueue
{
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
			return new BetterBeanstalkdJob($this->container, $this->pheanstalk, $job, $queue);
		}
	}
}