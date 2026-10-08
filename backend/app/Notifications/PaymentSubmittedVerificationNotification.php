<?php

namespace App\Notifications;

use App\Models\Application;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSubmittedVerificationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Application $application,
        public Payment $payment
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('बाह्रदशी गाउँपालिका — भुक्तानी प्रमाण पेश भयो र कागजात प्रमाणीकरण प्रक्रियामा रहेको जानकारी (#' . $this->application->application_number . ')')
            ->view('emails.payment-submitted', [
                'application' => $this->application,
                'payment' => $this->payment,
                'user' => $notifiable,
            ]);
    }
}
