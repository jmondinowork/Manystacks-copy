<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EquipmentEnrolled extends Notification
{
    use Queueable;

    protected $equipmentId;
    protected $equipmentName;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($equipmentId, string $equipmentName, string $message)
    {
        $this->equipmentId = $equipmentId;
        $this->equipmentName = $equipmentName;
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
            'title' => 'Gestion d\'équipement',
            'message' => $this->message,
            'equipment_id' => $this->equipmentId,
            'equipment_name' => $this->equipmentName,
            'link' => '/mes-equipements/' . $this->equipmentId,
            'type' => 'equipment',
        ];
    }
}
