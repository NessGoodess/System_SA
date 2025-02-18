<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'reference_number',
        'description',
        'created_by',
        'category_id',
        'status_id',
        'sender_department_id',
        'receiver_department_id',
        'issue_date',
        'received_date',
        'priority',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }

    /**
     * Get the category that owns the Document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the status that owns the Document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    /**
     * Get the sender_department that owns the Document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender_department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'sender_department_id');
    }

    /**
     * Get the departments that owns the Document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver_department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'receiver_department_id');
    }

    /**
     * Get the user that owns the Document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
