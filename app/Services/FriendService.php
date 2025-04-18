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

    // Reject Friend Request
    public function rejectFriendRequest($friendShipId) {
        return $this->friendRepository->rejectFriendRequest($friendShipId);
    }

    // Suggest Friend for sending friend request
    public function suggestFriends() {
        $userId = Auth::id();
        $users = $this->friendRepository->suggestFriend($userId);
        return $users;
    }

    // Get friendship
    public function getFriendship($friendId) {
        $userId = Auth::id();
        $friendship = $this->friendRepository->checkFriendShip($userId, (int) $friendId);
        if ($friendship) {
            return $friendship;
        } else {
            throw new \Exception("No friendship found");
        }
    }

    // Get Friend List
    public function getFriendList() {
        $userId = Auth::id();
        $friends = $this->friendRepository->getFriendList($userId);
        return $friends;
    }

    // Unfriend a user
    public function unFriend($friendId) {
        $userId = Auth::id();
        $friendShip = $this->friendRepository->unFriend($friendId, $userId);
        return $friendShip;
    }

    // Get Friend Request List
    public function getFriendRequestList() {
        $userId = Auth::id();
        $friendRequests = $this->friendRepository->getFriendRequestList($userId);
        return $friendRequests;
    }
}
