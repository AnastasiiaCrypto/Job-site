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

use App\Models\Message;
use App\Models\Payment;
use App\Models\Picture;
use App\Models\Post;
use App\Models\SavedPost;
use App\Models\Scopes\StrictActiveScope;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PostObserver
{
	/**
	 * Listen to the Entry deleting event.
	 *
	 * @param  Post $post
	 * @return void
	 */
	public function deleting(Post $post)
	{
		// Delete all Messages
		$messages = Message::where('post_id', $post->id)->get();
		if ($messages->count() > 0) {
			foreach ($messages as $message) {
				$message->delete();
			}
		}
		
		// Delete all Saved Posts
		$savedPosts = SavedPost::where('post_id', $post->id)->get();
		if ($savedPosts->count() > 0) {
			foreach ($savedPosts as $savedPost) {
				$savedPost->delete();
			}
		}
		
		// Remove logo files (if exists)
		if (empty($post->company_id)) {
			if (!empty($post->logo)) {
				$filename = str_replace('uploads/', '', $post->logo);
				if (!str_contains($filename, config('larapen.core.picture.default'))) {
					Storage::delete($filename);
				}
			}
		}
		
		// Delete all Pictures
		$pictures = Picture::where('post_id', $post->id)->get();
		if ($pictures->count() > 0) {
			foreach ($pictures as $picture) {
				$picture->delete();
			}
		}
		
		// Delete the Payment(s) of this Post
		$payments = Payment::withoutGlobalScope(StrictActiveScope::class)->where('post_id', $post->id)->get();
		if ($payments->count() > 0) {
			foreach ($payments as $payment) {
				$payment->delete();
			}
		}
	}
	
	/**
	 * Listen to the Entry saved event.
	 *
	 * @param  Post $post
	 * @return void
	 */
	public function saved(Post $post)
	{
		// Create a new email token if the post's email is marked as unverified
		if ($post->verified_email != 1) {
			if (empty($post->email_token)) {
				$post->email_token = md5(microtime() . mt_rand());
				$post->save();
			}
		}
		
		// Create a new phone token if the post's phone number is marked as unverified
		if ($post->verified_phone != 1) {
			if (empty($post->phone_token)) {
				$post->phone_token = mt_rand(100000, 999999);
				$post->save();
			}
		}
		
		// Removing Entries from the Cache
		$this->clearCache($post);
	}
	
	/**
	 * Listen to the Entry deleted event.
	 *
	 * @param  Post $post
	 * @return void
	 */
	public function deleted(Post $post)
	{
		// Remove the ad media folder
		if (!empty($post->country_code) && !empty($post->id)) {
			$directoryPath = 'files/' . strtolower($post->country_code) . '/' . $post->id;
			Storage::deleteDirectory($directoryPath);
		}
		
		// Removing Entries from the Cache
		$this->clearCache($post);
	}
	
	/**
	 * Removing the Entity's Entries from the Cache
	 *
	 * @param $post
	 */
	private function clearCache($post)
	{
		Cache::forget($post->country_code . '.sitemaps.posts.xml');
		
		Cache::forget($post->country_code . '.home.getPosts.sponsored');
		Cache::forget($post->country_code . '.home.getPosts.latest');
		Cache::forget($post->country_code . '.home.getFeaturedPostsCompanies');
		
		Cache::forget('post.withoutGlobalScopes.with.user.city.pictures.' . $post->id);
		Cache::forget('post.with.user.city.pictures.' . $post->id);
		
		Cache::forget('post.withoutGlobalScopes.with.user.city.pictures.' . $post->id . '.' . config('app.locale'));
		Cache::forget('post.with.user.city.pictures.' . $post->id . '.' . config('app.locale'));
		
		Cache::forget('posts.similar.category.' . $post->category_id . '.post.' . $post->id);
		Cache::forget('posts.similar.city.' . $post->city_id . '.post.' . $post->id);
	}
}
