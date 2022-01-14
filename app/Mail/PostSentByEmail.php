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
use App\Helpers\Arr;
use App\Models\Post;

class PostSentByEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $post;
    public $mailData;

    /**
     * Create a new message instance.
     *
     * @param Post $post
     * @param $mailData
     */
    public function __construct(Post $post, $mailData)
    {
        $this->post = $post;
        $this->mailData = (is_array($mailData)) ? Arr::toObject($mailData) : $mailData;

        $this->to($this->mailData->recipient_email, $this->mailData->recipient_email);
		$this->replyTo($this->mailData->sender_email, $this->mailData->sender_email);
        $this->subject(trans('mail.post_sent_by_email_title', [
            'appName'     => config('app.name'),
            'countryCode' => $post->country_code
        ]));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.post.sent-by-email');
    }
}
