<?php

namespace App\Respository;

use App\Models\Friend;
use App\Models\Profile;
use App\Models\User;
use App\Notifications\FriendRequestAcceptedNotification;
use App\Notifications\FriendRequestSentNotification;
use App\Respository\Interfaces\FriendRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class FriendRepository implements FriendRepositoryInterface {

    public function sendFriendRequest(int $userId, int $friendId) {
        if($userId === $friendId) {
            throw new \Exception("You cannot send friend request to yourself!");
        }

        $isFriend = $this->checkFriendShip($userId, $friendId);

        if( $isFriend ) {
            throw new \Exception("There is already a pending friend request or an existing friendship");
        }

        $friend = Friend::create([
            "user_id" => $userId,
            "friend_id" => $friendId,
            "requested_by" => $userId
        ]);

        $sender = User::with('profile')->find($userId);
        $recipient = User::with('profile')->find($friendId);

        if (!$recipient) {
            throw new \Exception("Recipient user not found");
        }

        $recipient->notify(new FriendRequestSentNotification($sender));

        return $friend;
    }

    public function checkFriendShip(int $userId, int $friendId) {
        return Friend::where(function ($query) use ($userId, $friendId) {
            $query->where("user_id", $userId)->where("friend_id", $friendId);
            $query->orWhere("user_id", $friendId)->where("friend_id", $userId);
        })->whereIn("status", ["pending", "accepted"])->first();
    }

    public function acceptFriendRequest(int $friendShipId) {
        $friendShip = Friend::where("id", $friendShipId)->first();
        if (!$friendShip) {
            throw new \Exception("There is no friendship available!");
        }
        if ($friendShip->friend_id !== Auth::id()) {
            throw new \Exception("You are not authorized to accept this friend request!");
        }
        if ($friendShip->status !== 'pending') {
            throw new \Exception("This friend request cannot be accepted!");
        }

        $updated = $friendShip->update(['status' => 'accepted']);
        if ($updated) {
            // Notify the user who sent the friend request
            $requester = User::with('profile')->find($friendShip->user_id);
            $accepter = User::with('profile')->find($friendShip->friend_id);

            if ($requester && $accepter) {
                $accepter->notifications()->whereRaw('JSON_EXTRACT(data, "$.sender_id") = ?', [$requester->id])->delete();
                $requester->notify(new FriendRequestAcceptedNotification($accepter));
            }
        }

        return $updated;
    }

    // Reject Friend Request
    public function rejectFriendRequest(int $friendShipId) {
        $friendShip = Friend::where("id", $friendShipId)->first();

        if (!$friendShip) {
            throw new \Exception("There is no friendship available!");
        }
        if ($friendShip->friend_id !== Auth::id()) {
            throw new \Exception("You are not authorized to accept this friend request!");
        }
        if ($friendShip->status !== 'pending') {
            throw new \Exception("You cannot accept a request that is not available!");
        }

        // Delete the friend Request Notification
        $requester = User::with('profile')->find($friendShip->user_id);
        $accepter = User::with('profile')->find($friendShip->friend_id);
        if ($requester && $accepter) {
            $accepter->notifications()->whereRaw('JSON_EXTRACT(data, "$.sender_id") = ?', [$requester->id])->delete();
        }

        return $friendShip->delete();
    }

    // Suggest Friend for send friend request
    public function suggestFriend(int $userId) {
        $relatedIds = Friend::where('user_id', $userId)
        ->whereIn('status', ['pending', 'accepted'])
        ->pluck('friend_id')
        ->merge(
            Friend::where('friend_id', $userId)
                ->whereIn('status', ['pending', 'accepted'])
                ->pluck('user_id')
        )
        ->unique() // Ensure no duplicate IDs
        ->toArray();

        // Combine related IDs with the logged-in user's ID to exclude them
        $excludedIds = array_merge($relatedIds, [$userId]);

        // Fetch profiles with their images, excluding users in $excludedIds
        return Profile::with('image')->whereNotIn('user_id', $excludedIds)->get();
    }

    // Get Friend List
    public function getFriendList(int $userId) {
        $friendIds = Friend::where('user_id', $userId)
            ->where('status', 'accepted')
            ->pluck('friend_id')
            ->merge(
                Friend::where('friend_id', $userId)
                    ->where('status', 'accepted')
                    ->pluck('user_id')
            )
            ->unique()
            ->toArray();

        // Fetch profiles with their images, excluding users in $excludedIds
        return Profile::with('image')->whereIn('user_id', $friendIds)->get();
    }
}
