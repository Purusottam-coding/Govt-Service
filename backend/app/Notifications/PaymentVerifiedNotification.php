<?php

namespace App\Notifications;

use App\Models\Application;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVerifiedNotification extends Notification
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
            ->subject('बाह्रदशी गाउँपालिका — भुक्तानी प्रमाणीकरण तथा निवेदन दर्ता पुष्टि (#' . $this->application->application_number . ')')
            ->view('emails.payment-verified', [
                'application' => $this->application,
                'payment' => $this->payment,
                'user' => $notifiable,
            ]);
    }
}
