<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'type'];

    public static function senders()
    {
        return self::where('type', 'sender')->orderBy('name')->get(['id', 'name']);
    }

    public static function receivers()
    {
        return self::where('type', 'receiver')->orderBy('name')->get(['id', 'name']);
    }

    /**
     * Get the users for the department
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all of the documents for the sender department
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentDocuments(): HasMany
    {
        return $this->hasMany(Document::class, 'sender_department_id');
    }

    /**
     * Get all of the documents for the receiver department
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function receivedDocuments()
    {
        return $this->hasMany(Document::class, 'receiver_department_id');
    }
}
