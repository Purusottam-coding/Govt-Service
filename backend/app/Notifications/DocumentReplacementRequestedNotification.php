<?php

namespace App\Notifications;

use App\Models\Application;
use App\Models\ApplicationDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentReplacementRequestedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Application $application,
        public ApplicationDocument $document,
        public ?string $reason = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('बाह्रदशी गाउँपालिका — कागजात पुनः अपलोड गर्न अनुरोध (#' . $this->application->application_number . ')')
            ->view('emails.document-replacement-requested', [
                'application' => $this->application,
                'document' => $this->document,
                'reason' => $this->reason,
                'user' => $notifiable,
            ]);
    }
}
