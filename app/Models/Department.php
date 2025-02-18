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
        return self::where ('type', 'receiver')->orderBy('name')->get(['id', 'name']);
    }

    /**
     * Get all of the documents for the Department
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
