<?php

namespace App\Respository;

use App\Models\Friend;
use App\Models\User;
use App\Respository\Interfaces\FriendRepositoryInterface;

class FriendRepository implements FriendRepositoryInterface {
    protected $model;

    public function __construct(Friend $model) {
        $this->model = $model;
    }

    public function sendFriendRequest(int $userId, int $friendId): mixed {
        return $this->model->create([
            "user_id" => $userId,
            "friend_id" => $friendId,
            "status" => "pending",
            "requested_by" => $userId,
        ]);
    }

    public function acceptFriendRequest(int $friendShipId): mixed {
        $friendShip = $this->model->findOrFail($friendShipId);
        $friendShip->update(['status' => 'accepted']);
        return $friendShip;
    }

    public function rejectFriendRequest(int $friendShipId): mixed {
        $friendShip = $this->model->findOrFail($friendShipId);
        $friendShip->delete();
        return $friendShip;
    }

    public function blockFriend(int $friendShipId): mixed {
        $friendShip = $this->model->findOrFail($friendShipId);
        $friendShip->update(['status' => 'blocked']);
        return $friendShip;
    }

    public function unblockFriend(int $friendShipId): mixed {
        $friendShip = $this->model->findOrFail($friendShipId);

        if($friendShip->status !== 'blocked') {
            throw new \Exception("Cannot unblock a friend that is not blocked");
        }

        $friendShip->update(['status', 'accepted']);
        return $friendShip;
    }

    public function unfriend(int $userId, int $friendId): mixed {
        $friendShip = $this->getFriendShip($userId, $friendId);

        if(!$friendShip) {
            throw new \Exception("You are not friends with this user");
        }
        if($friendShip->status !== "accepted") {
            throw new \Exception("You cannot unfriend a user that is not your friend");
        }
        $friendShip->delete();
        return $friendShip;
    }

    public function getPendingFriendRequest(int $userId): mixed {
        return $this->model->where('friend_id', $userId)->where('status', 'pending')->with('requester')->get();
    }

    public function getFriends(int $userId): mixed {
        return $this->model->where(function ($query) use ($userId) {
            $query->where('user_id', $userId)->orWhere('friend_id', $userId);
        })->where('status', 'accepted')->with('user', 'friend')->get();
    }

    public function getFriendShip(int $userId, int $friendId): mixed {
        return $this->model->where(function ($query) use ($userId, $friendId) {
            $query->where('user_id', $userId)->where('friend_id', $friendId);
        })->orWhere(function ($query) use ($userId, $friendId) {
            $query->where('user_id', $friendId)->where('friend_id', $userId);
        })->first();
    }

    // This method will be used to search for friends (Will be implemented properly later)
    public function findPotentialFriends(int $userId, string $searchTerm): mixed {
        $friendIds = $this->model->where(function ($query) use ($userId) {
            $query->where("user_id", $userId)->orWhere("friend_id", $userId);
        })->pluck("user_id")->merge(
            $this->model->where(function ($query) use ($userId) {
                $query->where("user_id", $userId)->orWhere("friend_id", $userId);
            })
        )->unique()->toArray();

        $friendIds[] = $userId;

        return User::whereNotIn('id', $friendIds)->where(
            function ($query) use ($searchTerm) {
                $query->where("username", "like", "%{$searchTerm}%")->orWhere('email', 'like', "%{$searchTerm}%")->orWhere("first_name", "like", "%{$searchTerm}%")->orWhere("last_name", "like", "%{$searchTerm}%");
            });
    }

    public function getSuggestedFriends(int $userId): mixed {
        $friendIds = $this->model->where(function ($query) use ($userId) {
            $query->where('user_id', $userId)->orWhere('friend_id', $userId);
        })
        ->where('status', 'accepted')
        ->pluck('user_id')
        ->merge(
            $this->model->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)->orWhere('friend_id', $userId);
            })
            ->where('status', 'accepted')
            ->pluck('friend_id')
        )
        ->unique()
        ->toArray();

        $existingRelationIds = $this->model->where(function ($query) use ($userId) {
            $query->where('user_id', $userId)->orWhere('friend_id', $userId);
        })
        ->pluck('user_id')
        ->merge(
            $this->model->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)->orWhere('friend_id', $userId);
            })->pluck('friend_id')
        )
        ->unique()
        ->toArray();

        $existingRelationIds[] = $userId;

        return User::select('users.*')
            ->join('friends as f1', function ($join) use ($friendIds) {
                $join->on('users.id', '=', 'f1.user_id')->orOn('users.id', '=', 'f1.friend_id')->whereIn('f1.user_id', $friendIds)->orWhereIn('f1.friend_id', $friendIds);
            })->whereNotIn('users.id', $existingRelationIds)
            ->where('f1.status', 'accepted')->with('profile')->groupBy('users.id')->orderByRaw('COUNT(*) DESC')->take(10)->get();
    }
}
