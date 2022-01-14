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

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;

class PaymentSent extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $post;

    /**
     * PaymentSent constructor.
     * @param Payment $payment
     * @param Post $post
     */
    public function __construct(Payment $payment, Post $post)
    {
        $this->payment = $payment;
        $this->post = $post;

        $this->to($post->email, $post->contact_name);
        $this->subject(trans('mail.payment_sent_title'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.payment.sent');
    }
}
