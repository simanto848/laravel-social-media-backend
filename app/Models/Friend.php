<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Friend extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
        'requested_by'
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationships
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friend() {
        return $this->belongsTo(Friend::class, 'friend_id');
    }

    public function requester() {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
