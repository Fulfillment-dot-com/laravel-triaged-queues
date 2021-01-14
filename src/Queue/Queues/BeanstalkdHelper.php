<?php


namespace Fulfillment\TriagedQueues\Queue\Queues\Beanstalkd;


use Illuminate\Queue\Jobs\BeanstalkdJob;

class BeanstalkdHelper
{
	/**
	 * @param BeanstalkdJob $job
	 */
	public static function touchJob(BeanstalkdJob $job)
	{
		$job->getPheanstalk()->touch($job->getPheanstalkJob());
	}
}