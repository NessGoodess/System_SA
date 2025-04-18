<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\User;
use App\Notifications\UserActivityNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordActivities implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue,SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public string $action,
        public $model = null,
        public ?string $description = null,
        public array $changes = [],
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        Activity::create([
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'action' => $this->action,
            'model_type' => $this->model ? class_basename($this->model) : null,
            'document_id' => $this->model?->id,
            'document_name' => $this->model?->name ?? $this->model?->title ?? null,
            'changes' => !empty($this->changes) ? json_encode($this->changes) : null,
            'description' => $this->description,
            'read_by_admin' => false,
        ]);

        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            Notification::send($admin, new UserActivityNotification($this->action, $this->user->name, $this->description));
        }
    }
}
