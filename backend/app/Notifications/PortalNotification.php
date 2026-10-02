<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PortalNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $message;
    public string $link;
    public string $icon;
    public string $color;
    public ?string $category;
    public array $meta;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        string $title,
        string $message,
        string $link = '#',
        string $icon = 'bell',
        string $color = 'primary',
        ?string $category = 'general',
        array $meta = []
    ) {
        $this->title = $title;
        $this->message = $message;
        $this->link = $link;
        $this->icon = $icon;
        $this->color = $color;
        $this->category = $category;
        $this->meta = $meta;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'link' => $this->link,
            'icon' => $this->icon,
            'color' => $this->color,
            'category' => $this->category,
            'meta' => $this->meta,
        ];
    }
}
