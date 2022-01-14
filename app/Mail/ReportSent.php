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

class ReportSent extends Mailable
{
	use Queueable, SerializesModels;
	
	public $post;
	public $report;
	
	/**
	 * Create a new message instance.
	 *
	 * @param Post $post
	 * @param $report
	 * @param $recipient
	 */
	public function __construct(Post $post, $report, $recipient)
	{
		$this->post = $post;
		$this->report = $report;
		
		$this->to($recipient->email, $recipient->name);
		$this->replyTo($this->report->email, $this->report->email);
		$this->subject(trans('mail.post_report_sent_title', [
			'appName'     => config('app.name'),
			'countryCode' => $post->country_code,
		]));
	}
	
	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->view('emails.post.report-sent');
	}
}
