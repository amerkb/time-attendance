<?php

namespace App\Events;

use App\Models\Alert;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use App\Statuses\NotificationType;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddAlertEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    protected $notifier;
    protected $alert;
    protected $user;


    public function __construct(User $notifier, Alert $alert, User $user)
    {
        $this->notifier = $notifier;
        $this->alert = $alert;
        $this->user = $user;

        if ($alert->type == 1) {
            $notification = new Notification();
            $notification->user_id = $user->id;
            $notification->company_id = $user->company_id;
            $notification->notifier_id = $notifier->id;
            $notification->type = NotificationType::ALERT;
            $notification->message =  "You Have New Alert From Admin Because SWEARING";
            $notification->save();
        } elseif ($alert->type == 2) {
            $notification = new Notification();
            $notification->user_id = $user->id;
            $notification->company_id = $user->company_id;
            $notification->notifier_id = $notifier->id;
            $notification->type = NotificationType::ALERT;
            $notification->message =  "You Have New Alert From Admin Because FABRICATE PROBLEMS";
            $notification->save();
        } else {
            $notification = new Notification();
            $notification->user_id = $user->id;
            $notification->company_id = $user->company_id;
            $notification->notifier_id = $notifier->id;
            $notification->type = NotificationType::ALERT;
            $notification->message =  "You Have New Alert From Admin For Many Reasons";
            $notification->save();
        }
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->notifier->id);
    }
    public function broadcastWith()
    {
        $notify = Notification::where('notifier_id', auth()->user()->id)->first();
        $unread_notifiy = Notification::where('read_at', null)->count();
        return [
            "data" => $notify,
            "unread_notifiy" => $unread_notifiy
        ];
    }

    public function toOneSignal()
    {

        $notify = Notification::where('notifier_id', auth()->user()->id)->first();
        $unread_notifiy = Notification::where('read_at', null)->count();
        return [
            "data" => $notify,
            "unread_notifiy" => $unread_notifiy
        ];
    }
}
