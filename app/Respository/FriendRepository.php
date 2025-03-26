<?php

namespace App\Respository;

use App\Models\Friend;
use App\Models\User;
use App\Respository\Interfaces\FriendRepositoryInterface;

class FriendRepository implements FriendRepositoryInterface {

    public function sendFriendRequest(int $userId, int $friendId) {
        if($userId === $friendId) {
            throw new \Exception("You cannot send friend request to yourself!");
        }

        $isFriend = $this->checkFriendShip($userId, $friendId);

        if( $isFriend ) {
            throw new \Exception("There is already a pending friend request or an existing friendship");
        }

        return Friend::create([
            "user_id" => $userId,
            "friend_id" => $friendId,
            "requested_by" => $userId
        ]);
    }

    public function checkFriendShip(int $userId, int $friendId) {
        return Friend::where(function ($query) use ($userId, $friendId) {
            $query->where("user_id", $userId)->where("friend_id", $friendId);
            $query->orWhere("user_id", $friendId)->where("friend_id", $userId);
        })->whereIn("status", ["pending", "accepted"])->first();
    }
}
