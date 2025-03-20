<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Profile extends Model
{
    /** @use HasFactory<\Database\Factories\ProfileFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'bio',
        'gender',
        'dob',
        'profile_image_id',
        'user_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function image() {
        return $this->belongsTo(Image::class, 'profile_image_id');
    }
    public function images() {
        return $this->morphMany(Image::class, 'imageable');
    }
}
