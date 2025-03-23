<?php

namespace App\Respository\Interfaces;

interface FriendRepositoryInterface {
    public function sendFriendRequest(int $userId, int $friendId): mixed;
    public function acceptFriendRequest(int $friendShipId): mixed;
    public function rejectFriendRequest(int $friendShipId): mixed;
    public function blockFriend(int $friendShipId): mixed;
    public function unblockFriend(int $friendShipId): mixed;
    public function unfriend(int $userId, int $friendId): mixed;
    public function getPendingFriendRequest(int $userId): mixed;
    public function getFriends(int $userId): mixed;
    public function getFriendShip(int $userId, int $friendId): mixed;
    public function findPotentialFriends(int $userId, string $searchTerm): mixed; # This method will be used to search for friends
    public function getSuggestedFriends(int $userId): mixed;
}
