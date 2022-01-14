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

namespace App\Http\Requests;

class RegisterRequest extends Request
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = [
			//'gender_id'  => 'required|not_in:0',
			'name'         => 'required|mb_between:2,200',
			'country_code' => 'sometimes|required|not_in:0',
			'phone'        => 'max:20',
			'email'        => 'max:100|whitelist_email|whitelist_domain',
			'password'     => 'required|between:6,60|dumbpwd|confirmed',
			'term'         => 'accepted',
		];
		
		// Email
		if ($this->filled('email')) {
			$rules['email'] = 'email|unique:users,email|' . $rules['email'];
		}
		if (isEnabledField('email')) {
			if (isEnabledField('phone') && isEnabledField('email')) {
				$rules['email'] = 'required_without:phone|' . $rules['email'];
			} else {
				$rules['email'] = 'required|' . $rules['email'];
			}
		}
		
		// Phone
		if (config('settings.sms.phone_verification') == 1) {
			if ($this->filled('phone')) {
				$countryCode = $this->input('country_code', config('country.code'));
				if ($countryCode == 'UK') {
					$countryCode = 'GB';
				}
				$rules['phone'] = 'phone:' . $countryCode . '|' . $rules['phone'];
			}
		}
		if (isEnabledField('phone')) {
			if (isEnabledField('phone') && isEnabledField('email')) {
				$rules['phone'] = 'required_without:email|' . $rules['phone'];
			} else {
				$rules['phone'] = 'required|' . $rules['phone'];
			}
		}
		if ($this->filled('phone')) {
			$rules['phone'] = 'unique:users,phone|' . $rules['phone'];
		}
		
		// Username
		if (isEnabledField('username')) {
			$rules['username'] = ($this->filled('username')) ? 'valid_username|allowed_username|between:3,100|unique:users,username' : '';
		}
		
		// COMPANY: Check 'resume' is required
		if (config('larapen.core.register.showCompanyFields')) {
			if ($this->input('user_type_id') == 1) {
				$rules['company.name'] = 'required|mb_between:2,200|whitelist_word_title';
				$rules['company.description'] = 'required|mb_between:5,1000|whitelist_word';
				
				// Check 'logo' is required
				if ($this->file('logo')) {
					$rules['logo'] = 'required|image|mimes:' . getUploadFileTypes('image') . '|max:' . (int)config('settings.upload.max_file_size', 1000);
				}
			}
		}
		
		// CANDIDATE: Check 'resume' is required
		if (config('larapen.core.register.showResumeFields')) {
			if ($this->input('user_type_id') == 2) {
				$rules['resume.filename'] = 'required|mimes:' . getUploadFileTypes('file') . '|max:' . (int)config('settings.upload.max_file_size', 1000);
			}
		}
		
		// Recaptcha
		if (config('settings.security.recaptcha_activation')) {
			$rules['g-recaptcha-response'] = 'required';
		}
		
		return $rules;
	}
	
	/**
	 * @return array
	 */
	public function messages()
	{
		$messages = [];
		
		return $messages;
	}
}
