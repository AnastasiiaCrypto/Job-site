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

use App\Models\Scopes\FromActivatedCategoryScope;
use App\Models\Scopes\LocalizedScope;
use App\Models\Scopes\VerifiedScope;
use App\Models\Scopes\ReviewedScope;
use App\Models\Traits\CountryTrait;
use App\Observer\PostObserver;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Jenssegers\Date\Date;
use Larapen\Admin\app\Models\Crud;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Post extends BaseModel implements Feedable
{
	use Crud, CountryTrait, Notifiable;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'posts';
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';
	protected $appends = ['uri', 'created_at_ta'];
	
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
		'country_code',
		'user_id',
		'company_id',
		'company_name',
		'logo',
		'company_description',
		'category_id',
		'post_type_id',
		'title',
		'description',
		'tags',
		'salary_min',
		'salary_max',
		'salary_type_id',
		'negotiable',
		'start_date',
		'application_url',
		'contact_name',
		'email',
		'phone',
		'phone_hidden',
		'city_id',
		'lat',
		'lon',
		'address',
		'ip_addr',
		'visits',
		'tmp_token',
		'email_token',
		'phone_token',
		'verified_email',
		'verified_phone',
		'reviewed',
		'featured',
		'archived',
		'partner',
		'created_at',
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
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];
	
	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/
	protected static function boot()
	{
		parent::boot();
		
		Post::observe(PostObserver::class);
		
		static::addGlobalScope(new FromActivatedCategoryScope());
		static::addGlobalScope(new VerifiedScope());
		static::addGlobalScope(new ReviewedScope());
		static::addGlobalScope(new LocalizedScope());
	}
	
	public function routeNotificationForMail()
	{
		return $this->email;
	}
	
	public function routeNotificationForNexmo()
	{
		$phone = phoneFormatInt($this->phone, $this->country_code);
		$phone = setPhoneSign($phone, 'nexmo');
		
		return $phone;
	}
	
	public function routeNotificationForTwilio()
	{
		$phone = phoneFormatInt($this->phone, $this->country_code);
		$phone = setPhoneSign($phone, 'twilio');
		
		return $phone;
	}
	
	public static function getFeedItems()
	{
		$postsPerPage = (int)config('settings.listing.items_per_page', 50);
		
		if (
			request()->has('d')
			|| config('plugins.domainmapping.installed')
		) {
			$countryCode = config('country.code');
			if (!config('plugins.domainmapping.installed')) {
				if (request()->has('d')) {
					$countryCode = request()->input('d');
				}
			}
			
			$posts = Post::where('country_code', $countryCode)
				->unarchived()
				->take($postsPerPage)
				->orderByDesc('id')
				->get();
		} else {
			$posts = Post::unarchived()->take($postsPerPage)->orderByDesc('id')->get();
		}
		
		return $posts;
	}
	
	public function toFeedItem()
	{
		$title = $this->title;
		$title .= (isset($this->city) && !empty($this->city)) ? ' - ' . $this->city->name : '';
		$title .= (isset($this->country) && !empty($this->country)) ? ', ' . $this->country->name : '';
		// $summary = str_limit(str_strip(strip_tags($this->description)), 5000);
		$summary = transformDescription($this->description);
		$link = config('app.locale') . '/' . $this->uri;
		
		return FeedItem::create()
			->id($link)
			->title($title)
			->summary($summary)
			->updated($this->updated_at)
			->link($link)
			->author($this->contact_name);
	}
	
	public function getTitleHtml()
	{
		$post = self::find($this->id);
		
		return getPostUrl($post);
	}
	
	public function getLogoHtml()
	{
		$style = ' style="width:auto; max-height:90px;"';
		
		// Get logo
		$out = '<img src="' . resize($this->logo, 'small') . '" data-toggle="tooltip" title="' . $this->title . '"' . $style . '>';
		
		// Add link to the Ad
		$url = localUrl($this->country_code, $this->uri);
		$out = '<a href="' . $url . '" target="_blank">' . $out . '</a>';
		
		return $out;
	}
	
	public function getPictureHtml()
	{
		// Get ad URL
		$url = url(config('app.locale') . '/' . $this->uri);
		
		$style = ' style="width:auto; max-height:90px;"';
		// Get first picture
		if ($this->pictures->count() > 0) {
			foreach ($this->pictures as $picture) {
				$url = localUrl($picture->post->country_code, $this->uri);
				$out = '<img src="' . resize($picture->filename, 'small') . '" data-toggle="tooltip" title="' . $this->title . '"' . $style . '>';
				break;
			}
		} else {
			// Default picture
			$out = '<img src="' . resize(config('larapen.core.picture.default'), 'small') . '" data-toggle="tooltip" title="' . $this->title . '"' . $style . '>';
		}
		
		// Add link to the Ad
		$out = '<a href="' . $url . '" target="_blank">' . $out . '</a>';
		
		return $out;
	}
	
	public function getCompanyNameHtml()
	{
		$out = '';
		$out .= $this->company_name;
		
		$out .= '<br>';
		$out .= '<small>';
		$out .= trans('admin::messages.By:') . ' ';
		if (isset($this->user) and !empty($this->user)) {
			$out .= $this->user->name;
		} else {
			$out .= '--';
		}
		$out .= '</small>';
		
		return $out;
	}
	
	public function getCityHtml()
	{
		if (isset($this->city) and !empty($this->city)) {
			if (config('settings.seo.multi_countries_urls')) {
				$uri = trans('routes.v-search-city', [
					'countryCode' => strtolower($this->city->country_code),
					'city'        => slugify($this->city->name),
					'id'          => $this->city->id,
				]);
			} else {
				$uri = trans('routes.v-search-city', [
					'city' => slugify($this->city->name),
					'id'   => $this->city->id,
				]);
			}
			
			if (!currentLocaleShouldBeHiddenInUrl()) {
				$uri = config('app.locale') . '/' . $uri;
			}
			
			return '<a href="' . localUrl($this->city->country_code, $uri) . '" target="_blank">' . $this->city->name . '</a>';
		} else {
			return $this->city_id;
		}
	}
	
	public function getReviewedHtml()
	{
		return ajaxCheckboxDisplay($this->{$this->primaryKey}, $this->getTable(), 'reviewed', $this->reviewed);
	}
	
	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function postType()
	{
		return $this->belongsTo(PostType::class, 'post_type_id', 'translation_of')->where('translation_lang', config('app.locale'));
	}
	
	public function category()
	{
		return $this->belongsTo(Category::class, 'category_id', 'translation_of')->where('translation_lang', config('app.locale'));
	}
	
	public function city()
	{
		return $this->belongsTo(City::class, 'city_id');
	}
	
	public function messages()
	{
		return $this->hasMany(Message::class, 'post_id');
	}
	
	public function latestPayment()
	{
		return $this->hasOne(Payment::class, 'post_id')->orderBy('id', 'DESC');
	}
	
	public function payments()
	{
		return $this->hasMany(Payment::class, 'post_id');
	}
	
	public function pictures()
	{
		return $this->hasMany(Picture::class, 'post_id')->orderBy('position')->orderBy('id', 'DESC');
	}
	
	public function savedByUsers()
	{
		return $this->hasMany(SavedPost::class, 'post_id');
	}
	
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	
	public function company()
	{
		return $this->belongsTo(Company::class, 'company_id');
	}
	
	public function salaryType()
	{
		return $this->belongsTo(SalaryType::class, 'salary_type_id', 'translation_of')->where('translation_lang', config('app.locale'));
	}
	
	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	public function scopeVerified($builder)
	{
		$builder->where(function ($query) {
			$query->where('verified_email', 1)->where('verified_phone', 1);
		});
		
		if (config('settings.single.posts_review_activation')) {
			$builder->where('reviewed', 1);
		}
		
		return $builder;
	}
	
	public function scopeUnverified($builder)
	{
		$builder->where(function ($query) {
			$query->where('verified_email', 0)->orWhere('verified_phone', 0);
		});
		
		if (config('settings.single.posts_review_activation')) {
			$builder->orWhere('reviewed', 0);
		}
		
		return $builder;
	}
	
	public function scopeArchived($builder)
	{
		return $builder->where('archived', 1);
	}
	
	public function scopeUnarchived($builder)
	{
		return $builder->where('archived', 0);
	}
	
	public function scopeReviewed($builder)
	{
		if (config('settings.single.posts_review_activation')) {
			return $builder->where('reviewed', 1);
		} else {
			return $builder;
		}
	}
	
	public function scopeUnreviewed($builder)
	{
		if (config('settings.single.posts_review_activation')) {
			return $builder->where('reviewed', 0);
		} else {
			return $builder;
		}
	}
	
	public function scopeWithCountryFix($builder)
	{
		// Check the Domain Mapping plugin
		if (config('plugins.domainmapping.installed')) {
			return $builder->where('country_code', config('country.code'));
		} else {
			return $builder;
		}
	}
	
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
	
	public function getDeletedAtAttribute($value)
	{
		$value = Date::parse($value);
		if (config('timezone.id')) {
			$value->timezone(config('timezone.id'));
		}
		
		return $value;
	}
	
	public function getCreatedAtTaAttribute($value)
	{
		$value = Date::parse($this->attributes['created_at']);
		if (config('timezone.id')) {
			$value->timezone(config('timezone.id'));
		}
		$value = $value->ago();
		
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
	
	public function getUriAttribute($value)
	{
		$value = trans('routes.v-post', [
			'slug' => slugify($this->attributes['title']),
			'id'   => $this->attributes['id'],
		]);
		
		return $value;
	}
	
	public function getTitleAttribute($value)
	{
		return mb_ucfirst($value);
	}
	
	public function getContactNameAttribute($value)
	{
		return mb_ucwords($value);
	}
	
	public function getCompanyNameAttribute($value)
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
		
		// Don't make an upload for Post->logo for logged users
		if (!str_contains(Route::currentRouteAction(), 'Admin\PostController')) {
			if (auth()->check()) {
				$this->attributes[$attribute_name] = $value;
				
				return $this->attributes[$attribute_name];
			}
		}
		
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
				})->encode($extension, config('larapen.core.picture.quality', 100));
				
				// Generate a filename.
				$filename = md5($value . time()) . '.' . $extension;
				
				// Store the image on disk.
				Storage::put($destination_path . '/' . $filename, $image->stream());
				
				// Save the path to the database
				$this->attributes[$attribute_name] = $destination_path . '/' . $filename;
				
				return $this->attributes[$attribute_name];
			} else {
				// Retrieve current value without upload a new file.
				if (starts_with($value, config('larapen.core.logo'))) {
					$value = null;
				} else {
					// Extract the value's country code
					$tmp = [];
					preg_match('#files/([A-Za-z]{2})/[\d]+#i', $value, $tmp);
					$valueCountryCode = (isset($tmp[1]) && !empty($tmp[1])) ? $tmp[1] : null;
					
					// Extract the value's ID
					$tmp = [];
					preg_match('#files/[A-Za-z]{2}/([\d]+)#i', $value, $tmp);
					$valueId = (isset($tmp[1]) && !empty($tmp[1])) ? $tmp[1] : null;
					
					// Extract the value's filename
					$tmp = [];
					preg_match('#files/[A-Za-z]{2}/[\d]+/(.+)#i', $value, $tmp);
					$valueFilename = (isset($tmp[1]) && !empty($tmp[1])) ? $tmp[1] : null;
					
					if (!empty($valueCountryCode) && !empty($valueId) && !empty($valueFilename)) {
						// Value's Path
						$valueDestinationPath = 'files/' . strtolower($valueCountryCode) . '/' . $valueId;
						if ($valueDestinationPath != $destination_path) {
							$oldFilePath = $valueDestinationPath . '/' . $valueFilename;
							$newFilePath = $destination_path . '/' . $valueFilename;
							
							// Copy the file
							Storage::copy($oldFilePath, $newFilePath);
							
							$this->attributes[$attribute_name] = $newFilePath;
							
							return $this->attributes[$attribute_name];
						}
					}
					
					if (!starts_with($value, 'files/')) {
						$value = $destination_path . last(explode($destination_path, $value));
					}
				}
				$this->attributes[$attribute_name] = $value;
				
				return $this->attributes[$attribute_name];
			}
		} catch (\Exception $e) {
			flash($e->getMessage())->error();
			$this->attributes[$attribute_name] = null;
			
			return false;
		}
	}
	
	public function setTagsAttribute($value)
	{
		$this->attributes['tags'] = (!empty($value)) ? mb_strtolower($value) : $value;
	}
	
	public function setApplicationUrlAttribute($value)
	{
		$this->attributes['application_url'] = (!empty($value)) ? strtolower($value) : $value;
	}
}
