<?php


namespace Fulfillment\TriagedQueues\Queue\Jobs;


use Illuminate\Container\Container;
use Illuminate\Queue\Jobs\SyncJob;

class BetterSyncJob extends SyncJob
{
	/**
	 * Create a new job instance.
	 *
	 * @param  \Illuminate\Container\Container $container
	 * @param  string                          $payload
	 * @param  string                          $queue
	 *
	 * @return void
	 */
	public function __construct(Container $container, $payload, $queue = null)
	{
		$this->queue = $queue;
		parent::__construct($container, $payload);
	}

	/**
	 * Stub for touch action
	 *
	 * @return void
	 */
	public function touch()
	{
		return null;
	}
}