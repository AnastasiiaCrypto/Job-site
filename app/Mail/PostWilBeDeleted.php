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

class PostWilBeDeleted extends Mailable
{
    use Queueable, SerializesModels;
    
    public $post;
    public $days;
    
    /**
     * Create a new message instance.
     *
     * @param Post $post
     * @param $days
     */
    public function __construct(Post $post, $days)
    {
        $this->post = $post;
        $this->days = $days;
        
        $this->to($post->email, $post->contact_name);
        $this->subject(trans('mail.post_will_be_deleted_title', [
            'title' => $post->title,
            'days'  => $days,
        ]));
    }
    
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.post.will-be-deleted');
    }
}
