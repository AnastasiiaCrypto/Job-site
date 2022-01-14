<?php
/**
 * JobClass - Geolocalized Job Board Script
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Observer;

use App\Models\TransactionState;
use Illuminate\Support\Facades\Cache;

class TransactionStateObserver
{
	/**
	 * Listen to the Entry saved event.
	 *
	 * @param  TransactionState $TransactionState
	 * @return void
	 */
	public function saved(TransactionState $TransactionState)
	{
		// Removing Entries from the Cache
		$this->clearCache($TransactionState);
	}
	
	/**
	 * Listen to the Entry deleted event.
	 *
	 * @param  TransactionState $TransactionState
	 * @return void
	 */
	public function deleted(TransactionState $TransactionState)
	{
		// Removing Entries from the Cache
		$this->clearCache($TransactionState);
	}
	
	/**
	 * Removing the Entity's Entries from the Cache
	 *
	 * @param $payment
	 */
	private function clearCache($country)
    {
        Cache::flush();
    }
}
