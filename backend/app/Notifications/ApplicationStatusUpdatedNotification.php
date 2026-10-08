<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusUpdatedNotification extends Notification
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
        $subject = match ($this->application->status) {
            'approved' => 'बाह्रदशी गाउँपालिका — निवेदन स्वीकृत भएको जानकारी' . (!empty($this->application->certificate_number) ? ' (प्रमाणपत्र ID: ' . $this->application->certificate_number . ')' : ''),
            'rejected' => 'बाह्रदशी गाउँपालिका — निवेदन अस्वीकृत भएको जानकारी (#' . $this->application->application_number . ')',
            default => 'बाह्रदशी गाउँपालिका — निवेदन स्थिति अद्यावधिक (#' . $this->application->application_number . ')',
        };

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.application-status-updated', [
                'application' => $this->application,
                'user' => $notifiable,
            ]);
    }
}
