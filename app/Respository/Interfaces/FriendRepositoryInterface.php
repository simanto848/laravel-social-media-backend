<?php

namespace App\Respository\Interfaces;

interface FriendRepositoryInterface {
    public function sendFriendRequest(int $userId, int $friendId);
    public function checkFriendShip(int $userId, int $friendId);
}
