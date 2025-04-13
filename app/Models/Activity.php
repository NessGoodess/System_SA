<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'action', // login, logout, view, create, update, delete
        'model_type',
        'document_id',
        'document_name',
        'changes',
        'description',
        'read_by_admin',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    /**
     * Get the user that owns the Activity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
