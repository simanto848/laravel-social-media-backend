<?php

namespace App\Http\Controllers;

use App\Services\FriendService;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    protected $friendService;

    public function __construct(FriendService $friendService)
    {
        $this->friendService = $friendService;
    }

    public function sendFriendRequest($friendId) {
        try {
            $friend = $this->friendService->sendFriendRequest($friendId);
            return $this->success($friend, "Send Friend Request");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage());
        }
    }
}
