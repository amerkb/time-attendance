<?php

namespace App\Events;

use App\Models\Comment;
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

class AddLikeToCommentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    protected $notifier;
    protected $post;
    protected $user;


    public function __construct(User $notifier, Comment $comment, User $user)
    {
        $this->notifier = $notifier;
        $this->post = $comment;
        $this->user = $user;

        $user_name = User::where('id', $user->id)->first();
        $notification = new Notification();
        $notification->user_id = $user->id;
        $notification->post_id = $comment->id;
        $notification->company_id = $user->company_id;
        $notification->notifier_id = $notifier->id;
        $notification->type = NotificationType::COMMENT_LIKE;
        $notification->message =  "New Like Added To Your Comment By " . $user_name->name;
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
