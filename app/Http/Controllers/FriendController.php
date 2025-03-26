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

    // Send Friend Request
    public function sendFriendRequest($friendId) {
        try {
            $friend = $this->friendService->sendFriendRequest($friendId);
            return $this->success($friend, "Send Friend Request");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage());
        }
    }

    // Accept Friend Request
    public function acceptFriendRequest($friendShipId) {
        try {
            $friend = $this->friendService->acceptFriendRequest($friendShipId);
            return $this->success($friend,"Friend Request accepted");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage());
        }
    }
}
