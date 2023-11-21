<?php

namespace App\Repository\Requests;

use App\Filter\VacationRequests\RequestFilter;
use App\Http\Resources\Deposit\DepositResource;
use App\Http\Resources\Deposit\DepositsRequestResource;
use App\Http\Resources\Requests\RequestResource;
use App\Http\Resources\Requests\RequestsDepositsResource;
use App\Http\Trait\UploadImage;
use App\Models\Attendance;
use App\Models\Deposit;
use App\Models\EmployeeAvailableTime;
use App\Models\Request;
use App\Repository\BaseRepositoryImplementation;
use App\Statuses\DepositStatus;
use App\Statuses\GenderStatus;
use App\Statuses\PaymentType;
use App\Statuses\RequestStatus;
use App\Statuses\RequestType;
use App\Statuses\UserTypes;
use App\Statuses\VacationRequestTypes;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestRepository extends BaseRepositoryImplementation
{
    use UploadImage;
    public function getFilterItems($filter)
    {
    }

    public function my_requests($filter)
    {
        $records = Request::query()->where('user_id', auth()->user()->id)->where('company_id', auth()->user()->company_id);
        if ($filter instanceof RequestFilter) {

            $records->when(isset($filter->type), function ($records) use ($filter) {
                $records->where('status', $filter->getRequestType());
            });

            $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                $records->orderBy($filter->getOrderBy(), $filter->getOrder());
            });

            return ['success' => true, 'data' => $records->get()];
        }
        return ['success' => true, 'data' => $records->get()];
    }


    public function my_approved_vacations_requests()
    {
        $records = Request::query()->where('user_id', auth()->user()->id)->where('company_id', auth()->user()->company_id)->where('type', RequestType::VACATION)->where('status', RequestStatus::APPROVEED);

        return ['success' => true, 'data' => $records->get()];
    }

    public function vacation_requests()
    {
        $records = Request::query()->where('type', RequestType::VACATION)->where('company_id', auth()->user()->company_id)->with('user')->where('status', RequestStatus::PENDING);
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {

            return ['success' => true, 'data' => $records->get()];
        } else {

            return ['success' => false, 'message' => "Unauthorized"];
        }
    }
    public function justify_requests()
    {
        $records = Request::query()->where('type', RequestType::JUSTIFY)->where('company_id', auth()->user()->company_id)->with('user')->where('status', RequestStatus::PENDING);
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {


            return ['success' => true, 'data' => $records->get()];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }
    public function retirement_requests()
    {
        $records = Request::query()->where('type', RequestType::RETIREMENT)->where('company_id', auth()->user()->company_id)->with('user')->where('status', RequestStatus::PENDING);
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {

            return ['success' => true, 'data' => $records->get()];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }
    public function resignation_requests()
    {
        $records = Request::query()->where('type', RequestType::RESIGNATION)->where('company_id', auth()->user()->company_id)->with('user')->where('status', RequestStatus::PENDING);
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {

            return ['success' => true, 'data' => $records->get()];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function all_requests()
    {
        $requests = Request::query()
            ->where('company_id', auth()->user()->company_id)
            ->with(['user' => function ($query) {
                $query->select('id', 'name', 'image', 'position');
            }])
            ->where('status', RequestStatus::PENDING)
            ->get(['id', 'user_id', 'vacation_type', 'payment_type', 'type', 'date', 'status', 'reason', 'start_date', 'end_date', 'start_time', 'end_time', 'reject_reason', 'attachments', 'justify_type', 'created_at']);

        $requests->transform(function ($request) {
            $request->user->image = $request->user->image ? asset($request->user->image) : null;
            return $request;
        });

        $deposit = Deposit::query()->where('company_id', auth()->user()->company_id)->with(['user' => function ($query) {
            $query->select('id', 'name', 'image', 'position', 'created_at');
        }])->where('status', DepositStatus::APPROVED)->where('extra_status', DepositStatus::UN_PAID)->get();

        $requestCollection = RequestsDepositsResource::collection($requests);
        $depositCollection = DepositsRequestResource::collection($deposit);

        $mergedCollection = $requestCollection->concat($depositCollection);

        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {
            return ['success' => true, 'data' => $mergedCollection];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }
    public function show_all_requests()
    {
        $requests = Request::query()
            ->where('company_id', auth()->user()->company_id)
            ->with(['user' => function ($query) {
                $query->select('id', 'name', 'image', 'position');
            }])
            ->get(['id', 'user_id', 'vacation_type', 'payment_type', 'type', 'date', 'status', 'reason', 'start_date', 'end_date', 'start_time', 'end_time', 'reject_reason', 'attachments', 'justify_type', 'created_at']);

        $requests->transform(function ($request) {
            $request->user->image = $request->user->image ? asset($request->user->image) : null;
            return $request;
        });

        $deposit = Deposit::query()->where('company_id', auth()->user()->company_id)->with(['user' => function ($query) {
            $query->select('id', 'name', 'image', 'position', 'created_at');
        }])->where('status', DepositStatus::APPROVED)->whereIn('extra_status', [DepositStatus::UN_PAID, DepositStatus::PAID, DepositStatus::UN_PAID_REJECTED])->get();

        $requestCollection = RequestsDepositsResource::collection($requests);
        $depositCollection = DepositsRequestResource::collection($deposit);

        $mergedCollection = $requestCollection->concat($depositCollection);

        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {
            return ['success' => true, 'data' => $mergedCollection];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function add_vacation_request($data)
    {
        DB::beginTransaction();

        try {
            $user_id = Auth::id();
            $current_date = date('Y-m-d');
            $existing_request = Request::where('user_id', $user_id)
                ->where('type', RequestType::VACATION)
                ->whereDate('created_at', $current_date)
                ->first();
            if ($existing_request) {
                return ['success' => false, 'message' => "A Vacation request has already been created for this user today,Please Contact With Hr."];
            }
            $start_date = $data['start_date'];
            $end_date = $data['end_date'];
            if (isset($data['start_time']) && isset($data['end_time'])) {
                $start_time = $data['start_time'];
                $end_time = $data['end_time'];
            }


            $availableTime = EmployeeAvailableTime::where('user_id', auth()->user()->id)->first();

            if (!$availableTime) {
                return ['success' => false, 'message' => "You Cannot Request Vacation Because You Don't Have Accrued Vacation Hours."];
            }
            $availableDailyHoursPerYear =  $availableTime->hourly_annual;
            $availableAnnualHoursPerYear =  $availableTime->daily_annual;

            if ($data['vacation_type'] == VacationRequestTypes::HOURLY && $data['payment_type'] == PaymentType::PAYMENT) {
                $existing_vacation_time = Request::where('start_date', $start_date)
                    ->where('end_date', $end_date)
                    ->orWhere(function ($query) use ($start_time, $end_time) {
                        $query->where('start_time', '<=', $end_time)
                            ->where('end_time', '>=', $start_time);
                    })->first();
                if ($existing_vacation_time) {
                    return ['success' => false, 'message' => "You Cannot request a vacation in this Time, Please Choose Another Time."];
                }

                $start = new DateTime($start_time);
                $end = new DateTime($end_time);
                $diff = $start->diff($end);
                $hours = $diff->format('%h.%i');

                if ($availableDailyHoursPerYear != 0 && $hours <=  $availableDailyHoursPerYear) {
                    $vacationRequest = new Request();
                    $vacationRequest->user_id = $user_id;
                    $vacationRequest->reason = $data['reason'];
                    $vacationRequest->type = RequestType::VACATION;
                    $vacationRequest->vacation_type = $data['vacation_type'];
                    $vacationRequest->status = RequestStatus::PENDING;
                    $vacationRequest->start_date = $data['start_date'];
                    $vacationRequest->end_date = $data['end_date'];
                    $vacationRequest->start_time = $data['start_time'];
                    $vacationRequest->end_time = $data['end_time'];
                    $vacationRequest->payment_type = $data['payment_type'];
                    $vacationRequest->company_id = auth()->user()->company_id;
                    if (Arr::has($data, 'attachments')) {
                        $file = Arr::get($data, 'attachments');

                        if ($file !== null && $file !== '' && is_file($file)) {
                            $file_name = $this->uploadEmployeeRequestsAttachment($file, $vacationRequest->id);
                            $vacationRequest->attachments = $file_name;
                        }
                    }

                    $vacationRequest->save();

                    // $availableTime->update([
                    //     'hourly_annual' => $availableTime->hourly_annual - $hours
                    // ]);
                } else {
                    return ['success' => false, 'message' => "You Cannot request a vacation Because Your Available Hours Ended."];
                }
            } elseif ($data['vacation_type'] == VacationRequestTypes::HOURLY && $data['payment_type'] == PaymentType::UNPAYMENT) {
                $existing_vacation_time = Request::where('start_date', $start_date)
                    ->where('end_date', $end_date)
                    ->orWhere(function ($query) use ($start_time, $end_time) {
                        $query->where('start_time', '<=', $end_time)
                            ->where('end_time', '>=', $start_time);
                    })->first();
                if ($existing_vacation_time) {
                    return ['success' => false, 'message' => "You Cannot request a vacation in this Time, Please Choose Another Time."];
                }

                $start = new DateTime($start_time);
                $end = new DateTime($end_time);
                $diff = $start->diff($end);
                $hours = $diff->format('%h.%i');

                if ($availableDailyHoursPerYear != 0 && $hours <=  $availableDailyHoursPerYear) {
                    $vacationRequest = new Request();
                    $vacationRequest->user_id = $user_id;
                    $vacationRequest->reason = $data['reason'];
                    $vacationRequest->type = RequestType::VACATION;
                    $vacationRequest->vacation_type = $data['vacation_type'];
                    $vacationRequest->status = RequestStatus::PENDING;
                    $vacationRequest->start_date = $data['start_date'];
                    $vacationRequest->end_date = $data['end_date'];
                    $vacationRequest->start_time = $data['start_time'];
                    $vacationRequest->end_time = $data['end_time'];
                    $vacationRequest->payment_type = $data['payment_type'];
                    $vacationRequest->company_id = auth()->user()->company_id;
                    if (Arr::has($data, 'attachments')) {
                        $file = Arr::get($data, 'attachments');
                        $file_name = $this->uploadEmployeeRequestsAttachment($file,  $vacationRequest->user_id);
                        $vacationRequest->attachments = $file_name;
                    }
                    $vacationRequest->save();
                } else {

                    return ['success' => false, 'message' => "You Cannot request a vacation Because Your Available Hours Ended."];
                }
            } elseif ($data['vacation_type'] == VacationRequestTypes::DAILY && $data['payment_type'] == PaymentType::PAYMENT) {

                $existing_vacation_time = Request::where('start_date', $start_date)
                    ->where('type', RequestType::VACATION)
                    ->where('end_date', $end_date)
                    ->first();
                if ($existing_vacation_time) {
                    return ['success' => false, 'message' => "You Cannot request a vacation in this Time, Please Choose Another Time."];
                }

                $start = new DateTime($start_date);
                $end = new DateTime($end_date);
                $diff = $start->diff($end);
                $days = $diff->days;

                if ($availableAnnualHoursPerYear != 0 && $days <=  $availableAnnualHoursPerYear) {
                    $vacationRequest = new Request();
                    $vacationRequest->user_id = $user_id;
                    $vacationRequest->reason = $data['reason'];
                    $vacationRequest->type = RequestType::VACATION;
                    $vacationRequest->vacation_type = $data['vacation_type'];
                    $vacationRequest->status = RequestStatus::PENDING;
                    $vacationRequest->start_date = $data['start_date'];
                    $vacationRequest->end_date = $data['end_date'];
                    $vacationRequest->payment_type = $data['payment_type'];
                    $vacationRequest->company_id = auth()->user()->company_id;
                    if (Arr::has($data, 'attachments')) {
                        $file = Arr::get($data, 'attachments');
                        $file_name = $this->uploadEmployeeRequestsAttachment($file,  $vacationRequest->user_id);
                        $vacationRequest->attachments = $file_name;
                    }
                    $vacationRequest->save();

                    // $availableTime->update([
                    //     'daily_annual' => $availableTime->daily_annual - $days
                    // ]);
                }
                else {
                    return ['success' => false, 'message' => "You Cannot request a vacation Because Your Available Hours Ended."];
                }
            } elseif ($data['vacation_type'] == VacationRequestTypes::METERNITY && auth()->user()->gender == GenderStatus::MALE) {
                return ['success' => false, 'message' => "You Cannot request a vacation For This Reason."];
            } elseif ($data['vacation_type'] == VacationRequestTypes::SATISFYING) {
                $vacationRequest = new Request();
                $vacationRequest->user_id = $user_id;
                $vacationRequest->reason = $data['reason'];
                $vacationRequest->type = RequestType::VACATION;
                $vacationRequest->vacation_type = $data['vacation_type'];
                $vacationRequest->status = RequestStatus::PENDING;
                $vacationRequest->start_date = $data['start_date'];
                $vacationRequest->end_date = $data['end_date'];
                $vacationRequest->payment_type = $data['payment_type'];
                $vacationRequest->person = $data['person'];
                $vacationRequest->company_id = auth()->user()->company_id;
                if (Arr::has($data, 'attachments')) {
                    $file = Arr::get($data, 'attachments');
                    $file_name = $this->uploadEmployeeRequestsAttachment($file,  $vacationRequest->user_id);
                    $vacationRequest->attachments = $file_name;
                }
                $vacationRequest->save();
            } elseif ($data['vacation_type'] == VacationRequestTypes::DEATH) {
                $vacationRequest = new Request();
                $vacationRequest->user_id = $user_id;
                $vacationRequest->reason = $data['reason'];
                $vacationRequest->type = RequestType::VACATION;
                $vacationRequest->vacation_type = $data['vacation_type'];
                $vacationRequest->status = RequestStatus::PENDING;
                $vacationRequest->start_date = $data['start_date'];
                $vacationRequest->end_date = $data['end_date'];
                $vacationRequest->payment_type = $data['payment_type'];
                $vacationRequest->dead_person = $data['dead_person'];
                $vacationRequest->degree_of_kinship = $data['degree_of_kinship'];
                $vacationRequest->company_id = auth()->user()->company_id;
                if (Arr::has($data, 'attachments')) {
                    $file = Arr::get($data, 'attachments');
                    $file_name = $this->uploadEmployeeRequestsAttachment($file,  $vacationRequest->user_id);
                    $vacationRequest->attachments = $file_name;
                }
                $vacationRequest->save();
            } else {
                $existing_vacation = Request::where('type', RequestType::VACATION)
                    ->where('start_date', $start_date)
                    ->orWhere('end_date', $end_date)
                    ->whereBetween('start_date', [$start_date, $end_date])
                    ->whereBetween('end_date', [$start_date, $end_date])
                    ->orWhere(function ($query) use ($start_date, $end_date) {
                        $query->where('start_date', '<=', $end_date)
                            ->where('end_date', '>=', $start_date);
                    })->first();

                $vacationRequest = new Request();
                $vacationRequest->user_id = $user_id;
                $vacationRequest->reason = $data['reason'];
                $vacationRequest->type = RequestType::VACATION;
                $vacationRequest->vacation_type = $data['vacation_type'];
                $vacationRequest->status = RequestStatus::PENDING;
                $vacationRequest->start_date = $data['start_date'];
                $vacationRequest->end_date = $data['end_date'];
                $vacationRequest->payment_type = $data['payment_type'];
                $vacationRequest->company_id = auth()->user()->company_id;
                if (Arr::has($data, 'attachments')) {
                    $file = Arr::get($data, 'attachments');
                    $file_name = $this->uploadEmployeeRequestsAttachment($file,  $vacationRequest->user_id);
                    $vacationRequest->attachments = $file_name;
                }
                $vacationRequest->save();
                if ($existing_vacation) {
                    return ['success' => false, 'message' => "You Cannot request a vacation in this Time, Please Choose Another Date."];
                }
            }
            DB::commit();
            if ($vacationRequest === null) {
                return ['success' => false, 'message' => "Vacation Request was not created"];
            }

            return ['success' => true, 'data' => $vacationRequest->load(['user'])];
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function add_justify_request($data)
    {
        DB::beginTransaction();
        try {
            $user_id = Auth::id();
            if (isset($data['date'])) {
                $existing_request = Request::where('user_id', $user_id)->where('type', RequestType::JUSTIFY)
                    ->whereDate('date', $data['date'])
                    ->first();

                if ($existing_request) {
                    return ['success' => false, 'message' => "A justify request has already been created for this user today."];
                }
            }

            if (isset($data['start_date']) && isset($data['end_date'])) {
                $existing_request_multi_date = Request::where('user_id', $user_id)
                    ->where('type', RequestType::JUSTIFY)
                    ->where(function ($query) use ($data) {
                        $query->where('start_date', $data['start_date'])
                            ->orWhere('end_date', $data['end_date'])
                            ->orWhere(function ($query) use ($data) {
                                $query->where('start_date', '<=', $data['end_date'])
                                    ->where('end_date', '>=', $data['end_date']);
                            });
                    })
                    ->first();
                if ($existing_request_multi_date) {
                    return ['success' => false, 'message' => "A justify request has already been created for this Days."];
                }
            }

            $justifyRequest = new Request();
            $justifyRequest->user_id =  $user_id;
            $justifyRequest->reason = $data['reason'];
            $justifyRequest->type = RequestType::JUSTIFY;
            $justifyRequest->justify_type = $data['justify_type'];

            $justifyRequest->status = RequestStatus::PENDING;
            $justifyRequest->company_id = auth()->user()->company_id;
            if (isset($data['date'])) {
                $justifyRequest->date = $data['date'];
            } else {
                $justifyRequest->start_date = $data['start_date'];
                $justifyRequest->end_date = $data['end_date'];
            }

            if (Arr::has($data, 'attachments')) {
                $file = Arr::get($data, 'attachments');

                if ($file !== null && $file !== '' && is_file($file)) {
                    $file_name = $this->uploadEmployeeRequestsAttachment($file, $justifyRequest->id);
                    $justifyRequest->attachments = $file_name;
                }
            }

            $justifyRequest->save();




            DB::commit();

            if ($justifyRequest === null) {
                return ['success' => false, 'message' => "Justify Request was not created"];
            }

            return ['success' => true, 'data' => $justifyRequest->load(['user'])];
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function add_retirement_request($data)
    {
        DB::beginTransaction();
        try {

            if (auth()->user()->type == UserTypes::HR || auth()->user()->type == UserTypes::EMPLOYEE) {
                $user_id = Auth::id();
                $existing_request = Request::where('user_id', $user_id)->where('type', RequestType::RETIREMENT)
                    ->whereIn('status', [RequestStatus::APPROVEED, RequestStatus::PENDING])
                    ->first();
                if ($existing_request) {
                    return ['success' => false, 'message' => "A Retirement request has already been created for this user."];
                }
                $employeeRequest = new Request();
                $employeeRequest->user_id =  auth()->user()->id;
                $employeeRequest->reason = $data['reason'];
                $employeeRequest->type = RequestType::RETIREMENT;
                $employeeRequest->status = RequestStatus::PENDING;
                $employeeRequest->date = date('Y-m-d');
                $employeeRequest->company_id = auth()->user()->company_id;

                if (Arr::has($data, 'attachments')) {
                    $file = Arr::get($data, 'attachments');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeRequestsAttachment($file, $employeeRequest->id);
                        $employeeRequest->attachments = $file_name;
                    }
                }

                $employeeRequest->save();
                DB::commit();
                if ($employeeRequest === null) {
                    return ['success' => false, 'message' => "Retirement Request was not created"];
                }
                return ['success' => true, 'data' => $employeeRequest->load(['user'])];
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function add_resignation_request($data)
    {
        DB::beginTransaction();
        try {
            if (auth()->user()->type == UserTypes::HR || auth()->user()->type == UserTypes::EMPLOYEE) {
                $user_id = Auth::id();
                $existing_request = Request::where('user_id', $user_id)->where('type', RequestType::RESIGNATION)
                    ->whereIn('status', [RequestStatus::APPROVEED, RequestStatus::PENDING])->first();
                if ($existing_request) {
                    return ['success' => false, 'message' => "A Resignation request has already been created for this user."];
                }
                $employeeRequest = new Request();
                $employeeRequest->user_id =  auth()->user()->id;
                $employeeRequest->reason = $data['reason'];
                $employeeRequest->type = RequestType::RESIGNATION;
                $employeeRequest->status = RequestStatus::PENDING;
                $employeeRequest->date = date('Y-m-d');
                $employeeRequest->company_id = auth()->user()->company_id;
                if (Arr::has($data, 'attachments')) {
                    $file = Arr::get($data, 'attachments');
                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeRequestsAttachment($file, $employeeRequest->id);
                        $employeeRequest->attachments = $file_name;
                    }
                }
                $employeeRequest->save();
                DB::commit();
                if ($employeeRequest === null) {
                    return ['success' => false, 'message' => "Resignation Request was not created"];
                }
                return ['success' => true, 'data' => $employeeRequest->load(['user'])];
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function reject_request($data)
    {
        DB::beginTransaction();
        try {
            $request = $this->getById($data['request_id']);
            if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type = UserTypes::HR && auth()->user()->company_id == $request->company_id) {

                $request->update([
                    'status' => RequestStatus::REJECTED,
                    'reject_reason' => $data['reject_reason']
                ]);

                DB::commit();
                if ($request === null) {
                    return ['success' => false, 'message' => "Vacation Request was not Updated"];
                }
                return ['success' => true, 'data' => $request->load('user')];
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function model()
    {
        return Request::class;
    }
}
