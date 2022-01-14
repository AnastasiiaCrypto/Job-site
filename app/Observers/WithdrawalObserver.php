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

use App\Models\Withdrawal;
use Illuminate\Support\Facades\Cache;

class WithdrawalObserver
{
    /**
     * Listen to the Entry saved event.
     *
     * @param  Withdrawal $withdrawal
     * @return void
     */
    public function saved(Withdrawal $withdrawal)
    {
        // Removing Entries from the Cache
        $this->clearCache($withdrawal);
    }
    
    /**
     * Listen to the Entry deleted event.
     *
     * @param  Withdrawal $withdrawal
     * @return void
     */
    public function deleted(Withdrawal $pawithdrawalge)
    {
        // Removing Entries from the Cache
        $this->clearCache($withdrawal);
    }
    
    /**
     * Removing the Entity's Entries from the Cache
     *
     * @param $withdrawal
     */
    private function clearCache($withdrawal)
    {
        Cache::flush();
    }
}
