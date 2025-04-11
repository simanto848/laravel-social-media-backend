<?php

namespace App\Services;


use App\Respository\PostRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class PostService {
    protected $postRepository;

    public function __construct(PostRepository $postRepository) {
        $this->postRepository = $postRepository;
    }

    public function createPost(array $data, array $images = []) {
        $data["user_id"] = Auth::id();

        $post = $this->postRepository->createPost($data);

        if (!empty($images)) {
            foreach ($images as $index => $image) {
                if ($image instanceof UploadedFile) {
                    try {
                        $path = $image->store('post_images', 'public');

                        $post->images()->create([
                            'path' => $path,
                            'imageable_type' => 'App\Models\Post',
                            'imageable_id' => $post->id,
                        ]);
                    } catch (\Exception $e) {
                        throw new $e("Error Processing Request", 1);

                    }
                } else {
                    throw new \Exception("Invalid image type");
                }
            }
        }

        return $post;
    }
}
