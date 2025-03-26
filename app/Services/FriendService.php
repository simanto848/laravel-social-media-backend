<?php

namespace App\Services;

use App\Respository\FriendRepository;
use Illuminate\Support\Facades\Auth;



class FriendService {
    protected $friendRepository;

    public function __construct(FriendRepository $friendRepository)
    {
        $this->friendRepository = $friendRepository;
    }

    // Send Friend Request
    public function sendFriendRequest($friendId) {
        $userId = Auth::id();

        $friend = $this->friendRepository->sendFriendRequest($userId, $friendId);
        return $friend;
    }

    // Accept Friend Request
    public function acceptFriendRequest($friendShipId) {
        return $this->friendRepository->acceptFriendRequest($friendShipId);
    }
}
