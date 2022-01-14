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

use App\Models\HomeSection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HomeSectionObserver
{
	/**
	 * Listen to the Entry updating event.
	 *
	 * @param  HomeSection $homeSection
	 * @return void
	 */
	public function updating(HomeSection $homeSection)
	{
		// Get the original object values
		$original = $homeSection->getOriginal();
		
		if (isset($original['options']) && !empty($original['options'])) {
			$original['options'] = jsonToArray($original['options']);
			
			// Remove old background_image from disk
			if (isset($homeSection->options['background_image']) && isset($original['options']['background_image'])) {
				if ($homeSection->options['background_image'] != $original['options']['background_image']) {
					Storage::delete($original['options']['background_image']);
				}
			}
		}
	}
	
	/**
	 * Listen to the Entry saved event.
	 *
	 * @param  HomeSection $homeSection
	 * @return void
	 */
	public function updated(HomeSection $homeSection)
	{
		//...
	}
	
    /**
     * Listen to the Entry saved event.
     *
     * @param  HomeSection $homeSection
     * @return void
     */
    public function saved(HomeSection $homeSection)
    {
        // Removing Entries from the Cache
        $this->clearCache($homeSection);
    }
    
    /**
     * Listen to the Entry deleted event.
     *
     * @param  HomeSection $homeSection
     * @return void
     */
    public function deleted(HomeSection $homeSection)
    {
        // Removing Entries from the Cache
        $this->clearCache($homeSection);
    }
    
    /**
     * Removing the Entity's Entries from the Cache
     *
     * @param $homeSection
     */
    private function clearCache($homeSection)
    {
		Cache::flush();
    }
}
