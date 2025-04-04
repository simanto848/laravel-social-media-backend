<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FriendRequestAcceptedNotification extends Notification
{
    use Queueable;

    protected $accepter;
    /**
     * Create a new notification instance.
     */
    public function __construct(User $accepter = null)
    {
        $this->accepter = $accepter;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'accepter_id' => $this->accepter ? $this->accepter->id : null,
            'message' => $this->accepter && $this->accepter->profile
                ? $this->accepter->profile->first_name . ' ' . $this->accepter->profile->last_name . ' accepted your friend request'
                : 'Someone accepted your friend request',
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
