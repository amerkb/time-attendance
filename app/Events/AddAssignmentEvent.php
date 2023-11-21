<?php

namespace App\Events;

use App\Models\Notification;
use App\Models\User;
use App\Statuses\NotificationType;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddAssignmentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    protected $notifier;
    protected $user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $notifier, User $user)
    {
        $this->notifier = $notifier;
        $this->user = $user;


        $user_name = User::where('id', $user->id)->first();
        $notification = new Notification();
        $notification->user_id = $user->id;
        $notification->company_id = $user->company_id;
        $notification->notifier_id = $notifier->id;
        $notification->type = NotificationType::ASSIGNMENT;
        $notification->message =  "A new Assignment has already been added to you By " .  $user_name->name;
        $notification->save();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->notifier->id);
    }
    public function broadcastWith()
    {
        $notify = Notification::where('notifier_id', auth()->user()->id)->latest()->first();
        $unread_notifiy = Notification::where('read_at', null)->where('notifier_id', auth()->user()->id)->count();
        return [
            "data" => $notify,
            "unread_notifiy" => $unread_notifiy
        ];
    }
}
