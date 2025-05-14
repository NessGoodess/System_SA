<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

    public function updatePhoto(?UploadedFile $photo = null): void
    {
        if (!$photo) return;

        if ($this->profile_photo && !str_starts_with($this->profile_photo, 'profile_photos/default/')) {
            Storage::disk('public')->delete($this->profile_photo);
        }

        $path = $photo->store('profile_photos', 'public');
        $this->profile_photo = $path;
        $this->save();
    }
}
