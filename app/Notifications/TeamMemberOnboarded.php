<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TeamMemberOnboarded extends Notification
{
    use Queueable;

    protected $collaboratorName;
    protected $action;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $collaboratorName, string $action, string $message)
    {
        $this->collaboratorName = $collaboratorName;
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
            'title' => 'Gestion d\'équipe',
            'message' => $this->message,
            'collaborator_name' => $this->collaboratorName,
            'action' => $this->action,
            'link' => '/mon-equipe',
            'type' => 'team',
        ];
    }
}
