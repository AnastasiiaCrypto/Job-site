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

use App\Models\Package;
use App\Models\PaymentMethod;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $post;
	public $package;
	public $paymentMethod;

    /**
     * PaymentNotification constructor.
     * @param $payment
     * @param $post
     * @param $adminUser
     */
    public function __construct($payment, $post, $adminUser)
    {
        $this->payment = $payment;
        $this->post = $post;
		$this->package = Package::findTrans($payment->package_id);
		$this->paymentMethod = PaymentMethod::find($payment->payment_method_id);

        $this->to($adminUser->email, $adminUser->name);
        $this->subject(trans('mail.payment_notification_title'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.payment.notification');
    }
}
