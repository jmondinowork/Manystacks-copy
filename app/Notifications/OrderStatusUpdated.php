<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    protected $reference;
    protected $status;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $reference, string $status, string $message)
    {
        $this->reference = $reference;
        $this->status = $status;
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
            'title' => 'Mise à jour de commande',
            'message' => $this->message,
            'reference' => $this->reference,
            'status' => $this->status,
            'link' => '/mes-commandes/' . $this->reference,
            'type' => 'order',
        ];
    }
}
