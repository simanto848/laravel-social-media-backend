<?php

namespace App\Respository\Interfaces;

interface FriendRepositoryInterface {
    public function sendFriendRequest(int $userId, int $friendId);
    public function checkFriendShip(int $userId, int $friendId);
    public function acceptFriendRequest(int $friendShipId);
    public function rejectFriendRequest(int $friendShipId);
    public function suggestFriend(int $userId);
    public function getFriendList(int $userId);
    public function getFriendRequestList(int $userId);
    public function unFriend(int $friendShipId, int $userId);
}
