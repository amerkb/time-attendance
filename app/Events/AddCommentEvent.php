<?php

namespace App\Events;

use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use App\Statuses\NotificationType;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddCommentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    protected $notifier;
    protected $post;
    protected $user;


    public function __construct(User $notifier, Post $post, User $user)
    {
        $this->notifier = $notifier;
        $this->post = $post;
        $this->user = $user;

        $user_name = User::where('id', $user->id)->first();
        $notification = new Notification();
        $notification->user_id = $user->id;
        $notification->post_id = $post->id;
        $notification->company_id = $user->company_id;
        $notification->notifier_id = $notifier->id;
        $notification->type = NotificationType::COMMENT;
        $notification->message =  "New Comment Added To Your Post By " . $user_name->name;
        $notification->save();
    }

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
