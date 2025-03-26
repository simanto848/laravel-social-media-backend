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

    public function sendFriendRequest($friendId) {
        $userId = Auth::id();

        $friend = $this->friendRepository->sendFriendRequest($userId, $friendId);
        return $friend;
    }
}
