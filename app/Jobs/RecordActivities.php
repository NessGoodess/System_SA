<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\User;
use App\Notifications\UserActivityNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordActivities implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue,SerializesModels;

    protected $user_id,$user_name,$action,$model_type,$document_id,$document_name,$changes,$description,$read_by_admin;

    /**
     * Create a new job instance.
     */
    public function __construct($user_id,$user_name,$action,$model_type,$document_id,$document_name,$changes,$description,$read_by_admin)
    {
        $this->user_id = $user_id;
        $this->user_name = $user_name;
        $this->action = $action;
        $this->model_type = $model_type;
        $this->document_id = $document_id;
        $this->document_name = $document_name;
        $this->changes = $changes;
        $this->description = $description;
        $this->read_by_admin = $read_by_admin;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $activity = Activity::create([
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'action' => $this->action,
            'model_type' => $this->model_type,
            'document_id' => $this->document_id,
            'document_name' => $this->document_name,
            'changes' => $this->changes,
            'description' => $this->description,
            'read_by_admin' => $this->read_by_admin,
        ]);

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new UserActivityNotification($activity));
        }
    }
}
