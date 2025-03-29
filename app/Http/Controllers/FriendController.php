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

    // Reject Friend Request
    public function rejectFriendRequest($friendShipId) {
        try {
            $this->friendService->rejectFriendRequest($friendShipId);
            return $this->success(null, "You removed friend request");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage());
        }
    }

    // Suggest Friend for sending friend request
    public function suggestFriends() {
        try {
            $users = $this->friendService->suggestFriends();
            return $this->success($users, "Suggested Friend List retrieved");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage());
        }
    }
}
