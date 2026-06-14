<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LicenseAssigned extends Notification
{
    use Queueable;

    protected $licenseName;
    protected $action;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $licenseName, string $action, string $message)
    {
        $this->licenseName = $licenseName;
        $this->action = $action;
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
            'title' => 'Attribution de licence',
            'message' => $this->message,
            'license_name' => $this->licenseName,
            'action' => $this->action,
            'link' => '/mes-licences',
            'type' => 'license',
        ];
    }
}
