<?php

namespace App\Notifications;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserActivityNotification extends Notification
{
    use Queueable;

    protected $activity;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $action,
        public string $user_name,
        public ?string $description,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => "Actividad: {$this->action}",
            'message' => "El usuario {$this->user_name} ha realizado la actividad: {$this->action}",
            'description' => $this->description,
        ];
    }
}
