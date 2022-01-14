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

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;
use App\Models\Message;
use Illuminate\Support\Facades\Storage;

class EmployerContacted extends Mailable
{
	use Queueable, SerializesModels;
	
	public $post;
	public $msg; // CAUTION: Conflict between the Model Message $message and the Laravel Mail Message objects
	
	/**
	 * Create a new message instance.
	 *
	 * @param Post $post
	 * @param Message $msg
	 */
	public function __construct(Post $post, Message $msg)
	{
		$this->post = $post;
		$this->msg = $msg;
		
		$this->to($post->email, $post->contact_name);
		$this->replyTo($msg->from_email, $msg->from_name);
		$this->subject(trans('mail.post_employer_contacted_title', [
			'title'   => $post->title,
			'appName' => config('app.name'),
		]));
	}
	
	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		$storagePath = Storage::getDriver()->getAdapter()->getPathPrefix();
		$pathToFile = $storagePath . $this->msg->filename;
		
		// Attachments
		if (file_exists($pathToFile)) {
			return $this->view('emails.post.employer-contacted')->attach($pathToFile);
		} else {
			return $this->view('emails.post.employer-contacted');
		}
	}
}
