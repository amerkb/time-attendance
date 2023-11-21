<?php


namespace App\Services\Deposit;

use App\Filter\Deposit\DepositFilter;
use App\Interfaces\Deposit\DepositServiceInterface;
use App\Models\Notification;
use App\Models\User;
use App\Repository\Deposit\DepositRepository;
use App\Services\Notifications\NotificationService;
use App\Statuses\DepositStatus;
use App\Statuses\NotificationType;
use App\Statuses\UserTypes;

class DepositService implements DepositServiceInterface
{

    public function __construct(private DepositRepository $depositRepository)
    {
    }
    public function create_deposit($data)
    {
        return $this->depositRepository->create_deposit($data);
    }

    public function approve_deposit($id)
    {
        $deposit = $this->depositRepository->getById($id);
        if ((auth()->user()->type == UserTypes::EMPLOYEE || auth()->user()->type == UserTypes::HR) && auth()->user()->company_id == $deposit['company_id'] && auth()->user()->id == $deposit->user_id) {

            $deposit->status = DepositStatus::APPROVED;
            $deposit->update();

            return ['success' => true, 'data' => $deposit->load('user')];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function reject_deposit($request)
    {
        $deposit = $this->depositRepository->getById($request['deposit_id']);
        if (auth()->user()->type == UserTypes::EMPLOYEE && auth()->user()->company_id == $deposit['company_id'] && auth()->user()->id == $deposit->user_id) {

            $deposit->status = DepositStatus::REJECTED;
            $deposit->reason_reject = $request['reason_reject'];
            $deposit->update();

            return ['success' => true, 'data' => $deposit->load('user')];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }


    public function clearance_request($id)
    {
        $deposit = $this->depositRepository->getById($id);
        if (auth()->user()->type == UserTypes::EMPLOYEE && auth()->user()->company_id == $deposit['company_id'] && auth()->user()->id == $deposit->user_id) {

            $deposit->extra_status = DepositStatus::UN_PAID;
            $deposit->clearance_request_date = date('Y-m-d');
            $deposit->update();

            return ['success' => true, 'data' => $deposit->load('user')];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function approve_clearance_request($id)
    {
        $deposit = $this->depositRepository->getById($id);
        if ((auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) && auth()->user()->company_id == $deposit['company_id']) {

            $deposit->extra_status = DepositStatus::PAID;
            $deposit->update();

            $notification = new Notification();
            $notification->user_id = auth()->user()->id;
            $notification->company_id = auth()->user()->company_id;
            $notification->notifier_id = $deposit->user_id;
            $notification->type = NotificationType::CLEARANCE_REQUEST;
            $notification->message =  "Your Clearance Request has been already Approved By " . auth()->user()->name;
            $notification->save();

            $title = "You Have New Notification";
            $body = $notification;
            $content = $notification->message;
            $type = "notification";

            $device_key = User::where('id', $deposit->user_id)->pluck('device_key')->first();

            NotificationService::sendNotification($device_key, $body, $title, $content, $type);

            return ['success' => true, 'data' => $deposit->load('user')];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function reject_clearance_request($request)
    {
        $deposit = $this->depositRepository->getById($request['deposit_id']);
        if ((auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) && auth()->user()->company_id == $deposit['company_id']) {

            $deposit->extra_status = DepositStatus::UN_PAID_REJECTED;
            $deposit->reason_clearance_reject = $request['reason_clearance_reject'];
            $deposit->update();

            $notification = new Notification();
            $notification->user_id = auth()->user()->id;
            $notification->company_id = auth()->user()->company_id;
            $notification->notifier_id = $deposit->user_id;
            $notification->type = NotificationType::CLEARANCE_REQUEST;
            $notification->message =  "Your Clearance Request has been already Rejected By " . auth()->user()->name;
            $notification->save();

            $title = "You Have New Notification";
            $body = $notification;
            $content = $notification->message;
            $type = "notification";

            $device_key = User::where('id', $deposit->user_id)->pluck('device_key')->first();

            NotificationService::sendNotification($device_key, $body, $title, $content, $type);

            return ['success' => true, 'data' => $deposit->load('user')];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }
    public function my_deposits(DepositFilter $depositFilter = null)
    {
        if ($depositFilter != null) {
            return $this->depositRepository->my_deposits($depositFilter);
        } else {
            $data = $this->depositRepository->get();
            return ['success' => true, 'data' => $data];
        }
    }

    public function list_of_deposits(DepositFilter $depositFilter = null)
    {
        if ($depositFilter != null) {
            return $this->depositRepository->list_of_deposits($depositFilter);
        } else {
            $data = $this->depositRepository->get();
            return ['success' => true, 'data' => $data];
        }
    }
    public function list_of_clearance_deposits()
    {
        return $this->depositRepository->list_of_clearance_deposits();
    }

    public function my_approved_deposits(DepositFilter $depositFilter = null)
    {
        if ($depositFilter != null)
            return $this->depositRepository->my_approved_deposits($depositFilter);
        else
            return $this->depositRepository->get();
    }
}