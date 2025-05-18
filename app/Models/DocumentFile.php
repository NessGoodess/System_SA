<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\Concerns\Has;

class DocumentFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'original_name',
        'stored_name',
        'file_path',
        'file_url',
        'mime_type',
        'file_extension',
        'file_size',
        'hash',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $hidden = ['hash'];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'file_size' => 'integer',
    ];

    /**
     * Get the document that owns the DocumentFile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the user that uploaded the file
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Format the file size to a human-readable format
     */

    public function getFormattedSizeAttribute(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($this->file_size, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
    }

    public function verifyIntegrity(): bool
    {
        if(!Storage::exists($this->file_path)) {
            return false;
        }

        return hash_file('sha256', Storage::path($this->file_path)) === $this->hash;
    }
}
