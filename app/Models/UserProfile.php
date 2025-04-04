<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_photo',
    ];
    //protected $appens = ['profile_photo_url'];

    /**
     * Get the user that owns the UserProfile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo ? asset('public/profiles/'. $this->profile_photo) : asset('public/profiles/avatar.svg');
    }*/
}
