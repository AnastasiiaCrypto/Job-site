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

namespace App\Models;

use App\Models\Scopes\LocalizedScope;
use App\Observer\CompanyObserver;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Jenssegers\Date\Date;
use Larapen\Admin\app\Models\Crud;

class Company extends BaseModel
{
	use Crud;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'companies';
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	// protected $primaryKey = 'id';
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var boolean
	 */
	public $timestamps = true;
	
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'name',
		'logo',
		'description',
		'country_code',
		'city_id',
		'address',
		'phone',
		'fax',
		'email',
		'website',
		'facebook',
		'twitter',
		'linkedin',
		'googleplus',
		'pinterest',
	];
	
	/**
	 * The attributes that should be hidden for arrays
	 *
	 * @var array
	 */
	// protected $hidden = [];
	
	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['created_at', 'updated_at'];
	
	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/
	protected static function boot()
	{
		parent::boot();
		
		Company::observe(CompanyObserver::class);
		static::addGlobalScope(new LocalizedScope());
	}
	
	public function getNameHtml()
	{
		$company = self::find($this->id);
		
		$out = '';
		if (!empty($company)) {
			$out .= '<a href="' . url(config('app.locale') . '/' . trans('routes.v-search-company', [
						'countryCode' => strtolower($company->country_code),
						'id'          => $company->id,
					])) . '" target="_blank">';
			$out .= $company->name;
			$out .= '</a>';
			$out .= ' <span class="label label-default">' . $company->posts()->count() . ' ' . trans('admin::messages.jobs') . '</span>';
		} else {
			$out .= '--';
		}
		
		return $out;
	}
	
	public function getLogoHtml()
	{
		$style = ' style="width:auto; max-height:90px;"';
		
		// Get logo
		$out = '<img src="' . resize($this->logo, 'small') . '" data-toggle="tooltip" title="' . $this->name . '"' . $style . '>';
		
		// Add link to the Ad
		$url = url(config('app.locale') . '/' . trans('routes.v-search-company', [
				'countryCode' => strtolower($this->country_code),
				'id'          => $this->id,
			]));
		$out = '<a href="' . $url . '" target="_blank">' . $out . '</a>';
		
		return $out;
	}
	
	public function getCountryHtml()
	{
		$iconPath = 'images/flags/16/' . strtolower($this->country_code) . '.png';
		if (file_exists(public_path($iconPath))) {
			$out = '';
			$out .= '<img src="' . url($iconPath) . getPictureVersion() . '" data-toggle="tooltip" title="' . $this->country_code . '">';
			
			return $out;
		} else {
			return $this->country_code;
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function posts()
	{
		return $this->hasMany(Post::class, 'company_id');
	}
	
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	
	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	
	/*
	|--------------------------------------------------------------------------
	| ACCESORS
	|--------------------------------------------------------------------------
	*/
	public function getCreatedAtAttribute($value)
	{
		$value = Date::parse($value);
		if (config('timezone.id')) {
			$value->timezone(config('timezone.id'));
		}
		//echo $value->format('l d F Y H:i:s').'<hr>'; exit();
		//echo $value->formatLocalized('%A %d %B %Y %H:%M').'<hr>'; exit(); // Multi-language
		
		return $value;
	}
	
	public function getUpdatedAtAttribute($value)
	{
		$value = Date::parse($value);
		if (config('timezone.id')) {
			$value->timezone(config('timezone.id'));
		}
		
		return $value;
	}
	
	public function getEmailAttribute($value)
	{
		if (
			isDemo() &&
			Request::segment(2) != 'password'
		) {
			if (auth()->check()) {
				if (auth()->user()->id != 1) {
					$value = hidePartOfEmail($value);
				}
			}
			
			return $value;
		} else {
			return $value;
		}
	}
	
	public function getPhoneAttribute($value)
	{
		$countryCode = config('country.code');
		if (isset($this->country_code) && !empty($this->country_code)) {
			$countryCode = $this->country_code;
		}
		
		$value = phoneFormatInt($value, $countryCode);
		
		return $value;
	}
	
	public function getNameAttribute($value)
	{
		return mb_ucwords($value);
	}
	
	public function getLogoFromOldPath()
	{
		if (!isset($this->attributes) || !isset($this->attributes['logo'])) {
			return null;
		}
		
		$value = $this->attributes['logo'];
		
		// Fix path
		$value = str_replace('uploads/pictures/', '', $value);
		$value = str_replace('pictures/', '', $value);
		$value = 'pictures/' . $value;
		
		if (!Storage::exists($value)) {
			$value = null;
		}
		
		return $value;
	}
	
	public function getLogoAttribute()
	{
		// OLD PATH
		$value = $this->getLogoFromOldPath();
		if (!empty($value)) {
			return $value;
		}
		
		// NEW PATH
		if (!isset($this->attributes) || !isset($this->attributes['logo'])) {
			$value = config('larapen.core.picture.default');
			
			return $value;
		}
		
		$value = $this->attributes['logo'];
		
		if (!Storage::exists($value)) {
			$value = config('larapen.core.picture.default');
		}
		
		return $value;
	}
	
	public static function getLogo($value)
	{
		// OLD PATH
		$value = str_replace('uploads/pictures/', '', $value);
		$value = str_replace('pictures/', '', $value);
		$value = 'pictures/' . $value;
		if (Storage::exists($value) && substr($value, -1) != '/') {
			return $value;
		}
		
		// NEW PATH
		$value = str_replace('pictures/', '', $value);
		if (!Storage::exists($value) && substr($value, -1) != '/') {
			$value = config('larapen.core.picture.default');
		}
		
		return $value;
	}
	
	/*
	|--------------------------------------------------------------------------
	| MUTATORS
	|--------------------------------------------------------------------------
	*/
	public function setLogoAttribute($value)
	{
		$attribute_name = 'logo';
		
		if (!isset($this->country_code) || !isset($this->id)) {
			$this->attributes[$attribute_name] = null;
			
			return false;
		}
		
		// Path
		$destination_path = 'files/' . strtolower($this->country_code) . '/' . $this->id;
		
		// If the image was erased
		if (empty($value)) {
			// delete the image from disk
			if (!str_contains($this->{$attribute_name}, config('larapen.core.picture.default'))) {
				Storage::delete($this->{$attribute_name});
			}
			
			// set null in the database column
			$this->attributes[$attribute_name] = null;
			
			return false;
		}
		
		// Check the image file
		if ($value == url('/')) {
			$this->attributes[$attribute_name] = null;
			
			return false;
		}
		
		// If laravel request->file('filename') resource OR base64 was sent, store it in the db
		try {
			if (fileIsUploaded($value)) {
				// Get file extension
				$extension = getUploadedFileExtension($value);
				if (empty($extension)) {
					$extension = 'jpg';
				}
				
				// Make the image (Size: 454x454)
				$image = Image::make($value)->resize(454, 454, function ($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				});
				
				// Generate a filename.
				$filename = md5($value . time()) . '.' . $extension;
				
				// Store the image on disk.
				Storage::put($destination_path . '/' . $filename, $image->stream());
				
				// Save the path to the database
				$this->attributes[$attribute_name] = $destination_path . '/' . $filename;
			} else {
				// Retrieve current value without upload a new file.
				if (!starts_with($value, 'files/')) {
					$value = $destination_path . last(explode($destination_path, $value));
				}
				$this->attributes[$attribute_name] = $value;
			}
		} catch (\Exception $e) {
			flash($e->getMessage())->error();
			$this->attributes[$attribute_name] = null;
			
			return false;
		}
	}
}
