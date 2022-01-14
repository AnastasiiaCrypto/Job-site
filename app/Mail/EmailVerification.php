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

class EmailVerification extends Mailable
{
	use Queueable, SerializesModels;
	
	public $entity;
	public $entityRef;
	
	/**
	 * EmailVerification constructor.
	 *
	 * @param $entity
	 * @param $entityRef
	 */
	public function __construct($entity, $entityRef)
	{
		$this->entity = $entity;
		$this->entityRef = $entityRef;
		
		$this->to($entity->email, $entity->{$entityRef['name']});
		$this->subject(trans('mail.email_verification_title', ['appName' => config('app.name')]));
	}
	
	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->view('emails.verification');
	}
}
