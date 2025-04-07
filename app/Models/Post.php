<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Post extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        "content",
        "image_id",
        "user_id",
        "is_public"
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function images() {
        return $this->morphMany(Image::class, "imageable");
    }

}
