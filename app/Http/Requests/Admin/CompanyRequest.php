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

namespace App\Http\Requests\Admin;

class CompanyRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Validation Rules
		$rules = [
			'name'        => 'required|mb_between:2,200|whitelist_word_title',
			'description' => 'required|mb_between:5,1000|whitelist_word',
		];
	
		// Check 'logo' is required
		if ($this->hasFile('logo')) {
			$rules['logo'] = 'required|image|mimes:' . getUploadFileTypes('image') . '|max:' . (int)config('settings.upload.max_file_size', 1000);
		}
        
        return $rules;
    }
}
