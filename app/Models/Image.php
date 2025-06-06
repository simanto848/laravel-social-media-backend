<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Image extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        "path",
        "imageable_id",
        "imageable_type"
    ];

    public function imageable()  {
        return $this->morphTo();
    }
}
