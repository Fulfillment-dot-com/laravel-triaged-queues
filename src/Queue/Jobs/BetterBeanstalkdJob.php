<?php


namespace Fulfillment\TriagedQueues\Queue\Jobs;


use Illuminate\Queue\Jobs\BeanstalkdJob;

class BetterBeanstalkdJob extends BeanstalkdJob
{
	/**
	 * Touch the job to reset time-left on TTR
	 *
	 * @return void
	 */
	public function touch()
	{
		$this->pheanstalk->touch($this->job);
	}
}