<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\EmployeeResource;
use App\Http\Resources\Notifications\NotificationResource;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Notifications
 * @authenticated
 * APIs for managing Notifications
 */
class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Show My Notifications List And Number Of Unread Notification
     *
     * This endpoint is used to display the list of my Notifications And Number Of Unread Notification and authenticate employee access to this API. It will show the Notifications specific to the authenticated employee.
     *
     * @response 200 scenario="Show My Notifications List And Number Of Unread Notification"{
     *"data": [
     *  {
     *  "id": 6,
     *  "notifier_id": 3,
     *  "post_id": 1,
     *  "user_id": 3,
     *  "message": "New Like Added To Your Post By mouaz alkhateeb",
     *   "created_at": "2 days ago"
     *},
     * {
     *     "id": 5,
     *     "notifier_id": 3,
     *    "post_id": 1,
     *     "user_id": 3,
     *     "message": "New Comment Added To Your Post By mouaz alkhateeb",
     *     "created_at": "2 days ago"
     * },
     * {
     * "id": 4,
     * "notifier_id": 3,
     * "post_id": null,
     * "user_id": 2,
     * "message": "You Have New Alert From Admin For Many Reasons",
     * "created_at": "2 days ago"
     * },
     * {
     *   "id": 3,
     *    "notifier_id": 3,
     *   "post_id": null,
     *    "user_id": 2,
     *    "message": "You Have New Alert From Admin Because FABRICATE PROBLEMS",
     *    "created_at": "2 days ago"
     * }
     *],
     *"unread_notification": 4
     *}
     */
    public function index()
    {
        $notifications = Notification::where('notifier_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $unread_notification = Notification::where('notifier_id', auth()->user()->id)->where('read_at', null)
            ->count();
        return response()->json([
            'data' => NotificationResource::collection($notifications),
            "unread_notification" => $unread_notification
        ]);
    }
    public static function GetUser($user_id)
    {
        $user = User::find($user_id);
        return EmployeeResource::make($user);
    }

    /**
     * Mark Notifications as Read
     *
     * This endpoint is used to Mark As Read My Notifications. Only authenticated employee can access this API.     *
     *
     * @response 200 {
     *     "data": {
     *         "message": "Notifications marked as read"
     *     }
     * }
     */

    public function markAsRead()
    {
        Notification::where('notifier_id', '=', Auth::id())
            ->whereNull('read_at')->update(['read_at' => Carbon::now()]);

        return [
            'message' => 'Notifications marked as read',
        ];
    }
}
