<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewSupportMessage extends Notification
{
    use Queueable;

    protected $supportId;
    protected $subject;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $supportId, string $subject, string $message)
    {
        $this->supportId = $supportId;
        $this->subject = $subject;
        $this->message = $message;
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
            'title' => 'Support',
            'message' => $this->message,
            'support_id' => $this->supportId,
            'subject' => $this->subject,
            'link' => '/supports/' . $this->supportId,
            'type' => 'support',
        ];
    }
}
