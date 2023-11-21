<?php

namespace App\Repository\Deposit;

use App\Events\AddAssignmentEvent;
use App\Filter\Deposit\DepositFilter;
use App\Http\Trait\UploadImage;
use App\Models\Deposit;
use App\Models\Notification;
use App\Models\User;
use App\Repository\BaseRepositoryImplementation;
use App\Services\Notifications\NotificationService;
use App\Statuses\DepositStatus;
use App\Statuses\DepositType;
use App\Statuses\NotificationType;
use App\Statuses\UserTypes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepositRepository extends BaseRepositoryImplementation
{
    use UploadImage;
    public function getFilterItems($filter)
    {
    }

    public function create_deposit($data)
    {
        DB::beginTransaction();
        try {

            if (auth()->user()->type == UserTypes::HR || auth()->user()->type == UserTypes::ADMIN) {

                $deposit = new Deposit();
                $deposit->status = DepositStatus::PENDING;
                $deposit->user_id = $data['user_id'];
                $deposit->company_id = auth()->user()->company_id;

                if ($data['type'] == DepositType::CAR) {

                    $deposit->type = DepositType::CAR;
                    $deposit->deposit_request_date = date('Y-m-d');
                    $deposit->car_number = $data['car_number'];
                    $deposit->car_model = $data['car_model'];
                    $deposit->manufacturing_year = $data['manufacturing_year'];
                    $deposit->Mechanic_card_number = $data['Mechanic_card_number'];

                    if (Arr::has($data, 'car_image')) {
                        $file = Arr::get($data, 'car_image');

                        if ($file !== null && $file !== '' && is_file($file)) {
                            $file_name = $this->uploadEmployeeDepositsAttachment($file, $deposit->id);
                            $deposit->car_image = $file_name;
                        }
                    }
                } elseif ($data['type'] == DepositType::LAPTOP) {
                    $deposit->type = DepositType::LAPTOP;
                    $deposit->deposit_request_date = date('Y-m-d');
                    $deposit->laptop_type = $data['laptop_type'];
                    $deposit->serial_laptop_number = $data['serial_laptop_number'];
                    $deposit->laptop_color = $data['laptop_color'];

                    if (Arr::has($data, 'laptop_image')) {
                        $file = Arr::get($data, 'laptop_image');

                        if ($file !== null && $file !== '' && is_file($file)) {
                            $file_name = $this->uploadEmployeeDepositsAttachment($file, $deposit->id);
                            $deposit->laptop_image = $file_name;
                        }
                    }
                } elseif ($data['type'] == DepositType::MOBILE) {
                    $deposit->type = DepositType::MOBILE;
                    $deposit->deposit_request_date = date('Y-m-d');
                    $deposit->serial_mobile_number = $data['serial_mobile_number'];
                    $deposit->mobile_color = $data['mobile_color'];
                    $deposit->mobile_type = $data['mobile_type'];
                    $deposit->mobile_sim = $data['mobile_sim'];

                    if (Arr::has($data, 'mobile_image')) {
                        $file = Arr::get($data, 'mobile_image');

                        if ($file !== null && $file !== '' && is_file($file)) {
                            $file_name = $this->uploadEmployeeDepositsAttachment($file, $deposit->id);
                            $deposit->mobile_image = $file_name;
                        }
                    }
                }
                $deposit->save();

                $user = Auth::user();
                $notifier = $deposit->user;


                if ($notifier->device_key != null) {
                    if ($notifier->id != auth()->user()->id) {
                        $notification = new Notification();
                        $notification->user_id = auth()->user()->id;
                        $notification->company_id = auth()->user()->company_id;
                        $notification->notifier_id = $deposit->user_id;
                        $notification->type = NotificationType::ASSIGNMENT;
                        $notification->message =  "A new Assignment has already been added to you By " . auth()->user()->name;
                        $notification->save();

                        $title = "You Have New Notification";
                        $body = $notification;
                        $content = $notification->message;
                        $type = "notification";

                        $device_key = User::where('id',  $notifier->id)->pluck('device_key')->first();

                        NotificationService::sendNotification($device_key, $body, $title, $content, $type);
                    }
                } else {
                    if ($notifier->id != auth()->user()->id) {
                        event(new AddAssignmentEvent($notifier, $user));
                    }
                }


                DB::commit();
                if ($deposit === null) {
                    return ['success' => false, 'message' => "Deposit was not created"];
                }
                return ['success' => true, 'data' => $deposit->load('user')];
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function my_deposits($filter)
    {
        if (auth()->user()->type == UserTypes::EMPLOYEE || auth()->user()->type == UserTypes::HR) {
            $records = Deposit::query()->where('company_id', auth()->user()->company_id)->where('user_id', auth()->user()->id)->where('extra_status', null)
                ->where('status', DepositStatus::PENDING);
            if ($filter instanceof DepositFilter) {

                $records->when(isset($filter->status), function ($records) use ($filter) {
                    $records->where('status', $filter->getStatus());
                });
                $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                    $records->orderBy($filter->getOrderBy(), $filter->getOrder());
                });

                $deposits = $records->paginate($filter->per_page);
                return ['success' => true, 'data' => $deposits];
            }
            $deposits = $records->paginate($filter->per_page);
            return ['success' => true, 'data' => $deposits];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }
    public function list_of_deposits($filter)
    {
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {
            $records = Deposit::query()
                ->where('company_id', auth()->user()->company_id)
                ->whereIn('status', [DepositStatus::APPROVED, DepositStatus::PENDING, DepositStatus::REJECTED])
                ->where(function ($query) {
                    $query->whereIn('extra_status', [DepositStatus::UN_PAID, DepositStatus::UN_PAID_REJECTED])
                        ->orWhereNull('extra_status');
                });
            if ($filter instanceof DepositFilter) {

                $records->when(isset($filter->status), function ($records) use ($filter) {
                    $records->where('status', $filter->getStatus());
                });
                $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                    $records->orderBy($filter->getOrderBy(), $filter->getOrder());
                });

                $deposits = $records->with('user')->get();
                return ['success' => true, 'data' => $deposits];
            }
            $deposits = $records->with('user')->get();
            return ['success' => true, 'data' => $deposits];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }
    public function list_of_clearance_deposits()
    {
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {
            $records = Deposit::query()->where('company_id', auth()->user()->company_id)->where('status', DepositStatus::APPROVED)->where('extra_status', DepositStatus::UN_PAID);
            $deposits = $records->with('user')->get();
            return ['success' => true, 'data' => $deposits];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function my_approved_deposits($filter)
    {
        $user = auth()->user();
        $deposits = Deposit::where('user_id', $user->id)
            ->where('status', DepositStatus::APPROVED)
            ->where(function ($query) {
                $query->whereNull('extra_status')
                    ->orWhere('extra_status', '!=', DepositStatus::PAID);
            })
            ->get();
        return ['success' => true, 'data' => $deposits];
    }

    public function model()
    {
        return Deposit::class;
    }
}
