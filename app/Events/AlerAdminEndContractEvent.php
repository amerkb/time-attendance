<?php

namespace App\Events;

use App\Models\Notification;
use App\Models\User;
use App\Statuses\NotificationType;
use App\Statuses\UserTypes;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AlerAdminEndContractEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    protected $user;
    protected $admins;
    public function __construct(User $user)
    {
        $this->user = $user;
        $admins = User::where('type', UserTypes::ADMIN)->get();
        $this->admins = $admins;

        foreach ($this->admins  as $admin) {
            $notification = new Notification();
            $notification->user_id = $this->user->id;
            $notification->company_id = $user->company_id;
            $notification->notifier_id = $admin->id;
            $notification->type = NotificationType::CONTRACT_EXPIRED;
            $notification->message =  "Employee Contract " .  $this->user->name . " Almost expired";
            $notification->save();
        }
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.admins');
    }

    public function broadcastWith()
    {
        foreach ($this->admins  as $admin) {
            $unread_notifications = Notification::where('user_id', $admin->id)
                ->whereNull('read_at')
                ->count();
        }

        return [
            "data" => $this->user,
            "unread_notifications" => $unread_notifications
        ];
    }
}
