<?php

namespace App\Respository;

use App\Models\Post;
use App\Respository\Interfaces\PostRepositoryInterface;

class PostRepository implements PostRepositoryInterface {
    public function createPost(array $data) {
        return Post::create($data);
    }
}
