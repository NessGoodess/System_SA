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
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
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
            'user_id' => $this->activity->user_id,
            'user_name' => $this->activity->user_name,
            'action' => $this->activity->action,
            'model_type' => $this->activity->model_type,
            'document_id' => $this->activity->document_id,
            'document_name' => $this->activity->document_name,
            'changes' => $this->activity->changes,
            'description' => $this->activity->description,
            'read_by_admin' => $this->activity->read_by_admin,
        ];
    }
}
