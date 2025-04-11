<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService){
        $this->postService = $postService;
    }

    public function createPost(Request $request) {
        try {
            $validateData = $request->validate([
                'content' => ['string'],
                'is_public' => ['boolean'],
                'images.*' => ['image', 'max:2048']
            ]);
            $postData = [
                'content' => $validateData['content'] ?? null,
                'is_public' => $validateData['is_public'] ?? true
            ];

            $images = [];
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                $images = is_array($files) ? $files : [$files];
            }

            // Create post
            $post = $this->postService->createPost($postData, $images);
            return $this->success($post, "Post created successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage());
        }
    }
}
