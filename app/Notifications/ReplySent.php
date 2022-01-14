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

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class ReplySent extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        if (!empty($this->message->to_email)) {
            if (config('settings.sms.message_activation') == 1) {
                if (!empty($this->message->to_phone)) {
                    if (config('settings.sms.driver') == 'twilio') {
                        return ['mail', TwilioChannel::class];
                    }
        
                    return ['mail', 'nexmo'];
                }
    
                return ['mail'];
            } else {
                return ['mail'];
            }
        } else {
            if (config('settings.sms.driver') == 'twilio') {
                return [TwilioChannel::class];
            }

            return ['nexmo'];
        }
    }

    public function toMail($notifiable)
    {
        return (new \App\Mail\ReplySent($this->message));
    }

    public function toNexmo($notifiable)
    {
        return (new NexmoMessage())->content($this->smsMessage())->unicode();
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())->content($this->smsMessage());
    }

    protected function smsMessage()
    {
        return trans('sms.reply_form_content', [
            'appName' => config('app.name'),
            'subject' => $this->message->subject,
            'message' => str_limit(strip_tags($this->message->message), 50)
        ]);
    }
}
