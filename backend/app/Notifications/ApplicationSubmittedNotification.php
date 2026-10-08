<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public Application $application)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('बाह्रदशी गाउँपालिका — सेवा निवेदन दर्ता भएको जानकारी (#' . $this->application->application_number . ')')
            ->view('emails.application-submitted', [
                'application' => $this->application,
                'user' => $notifiable,
            ]);
    }
}
