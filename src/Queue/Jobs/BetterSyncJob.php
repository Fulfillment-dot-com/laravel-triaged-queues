<?php


namespace Fulfillment\TriagedQueues\Queue\Jobs;

use Illuminate\Queue\Jobs\SyncJob;

class BetterSyncJob extends SyncJob
{
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