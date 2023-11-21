<?php

namespace App\Repository\Alerts;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\Result;
use App\Events\AddAlertEvent;
use App\Filter\Alerts\AlertFilter;
use App\Models\Alert;
use App\Models\Notification;
use App\Models\User;
use App\Repository\BaseRepositoryImplementation;
use App\Services\Notifications\NotificationService;
use App\Statuses\NotificationType;
use App\Statuses\UserTypes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AlertRepository extends BaseRepositoryImplementation
{
    public function getFilterItems($filter)
    {
        $records = Alert::query()->where('email', '=', Auth::user()->email)->with('user');
        if ($filter instanceof AlertFilter) {

            $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                $records->orderBy($filter->getOrderBy(), $filter->getOrder());
            });


            return $records->get();
        }
        return $records->get();
    }

    public function all_alerts($filter)
    {
        $records = Alert::query()->where('company_id', auth()->user()->company_id)->with('user');
        if ($filter instanceof AlertFilter) {
            $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                $records->orderBy($filter->getOrderBy(), $filter->getOrder());
            });
            return $records->get();
        }
        return $records->get();
    }


    public function create_alert($data)
    {
        DB::beginTransaction();
        try {

            if (auth()->user()->type == UserTypes::ADMIN) {

                $alert = new Alert();
                $alert->email = $data['email'];
                $alert->content = $data['content'];
                $alert->type = $data['type'];
                $alert->company_id = auth()->user()->company_id;
                $alert->user_id = auth()->user()->id;
                $alert->save();

                $user = Auth::user();
                $alert = Alert::findOrFail($alert->id);
                $notifier = User::where('email', $alert->email)->first();

                $title = "You Have New Notification";
                $body = $alert;
                $device_key = User::where('email', $alert->email)->pluck('device_key')->first();

                if ($notifier->device_key != null) {
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
                    $content = $notification->message;
                    $type = "alert";
                    NotificationService::sendNotification($device_key, $body, $title, $content, $type);
                } else {
                    event(new AddAlertEvent($notifier, $alert, $user));
                }

                DB::commit();

                if ($alert === null) {
                    return ['success' => false, 'message' => "Alert was not created"];
                }
                return ['success' => true, 'data' => $alert->load('user')];
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    /**
     * Specify Model class name.
     * @return mixed
     */
    public function model()
    {
        return Alert::class;
    }
}
