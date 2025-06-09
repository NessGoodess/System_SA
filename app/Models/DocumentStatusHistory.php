<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentStatusHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'document_id',
        'status_id',
        'comment',
        'form',
    ];

    protected $casts = [
        'form' => 'array',
    ];

    /**
     * Get the document that owns the DocumentStatusHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the status that owns the DocumentStatusHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
