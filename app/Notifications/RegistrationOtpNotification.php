<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationOtpNotification extends Notification
{
    use Queueable;

    public function __construct(public string $otp)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('बाह्रदशी गाउँपालिका — नागरिक खाता प्रमाणीकरण कोड (OTP: ' . $this->otp . ')')
            ->view('emails.registration-otp', [
                'user' => $notifiable,
                'otp' => $this->otp,
            ]);
    }
}
