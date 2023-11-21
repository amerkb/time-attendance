<?php

namespace App\Services\Requests;

use App\Filter\VacationRequests\RequestFilter;
use App\Models\Attendance;
use App\Models\EmployeeAvailableTime;
use App\Models\Request;
use App\Models\User;
use App\Query\Employee\GetMonthlyShiftQuery;
use App\Repository\Requests\RequestRepository;
use App\Statuses\EmployeeStatus;
use App\Statuses\PaymentType;
use App\Statuses\RequestStatus;
use App\Statuses\RequestType;
use App\Statuses\UserTypes;
use App\Statuses\VacationRequestTypes;
use Carbon\Carbon;
use DateTime;

class RequestService
{
    public function __construct(private RequestRepository $requestRepository, private GetMonthlyShiftQuery $getMonthlyShiftQuery)
    {
    }

    public function add_vacation_request($data)
    {
        return $this->requestRepository->add_vacation_request($data);
    }

    public function add_justify_request($data)
    {
        return $this->requestRepository->add_justify_request($data);
    }
    public function add_retirement_request($data)
    {
        return $this->requestRepository->add_retirement_request($data);
    }
    public function add_resignation_request($data)
    {
        return $this->requestRepository->add_resignation_request($data);
    }

    public function show($id)
    {
        $request = Request::where('id', $id)->first();
        if ((auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) && auth()->user()->company_id == $request->company_id) {
            return ['success' => true, 'data' => $this->requestRepository->with('user')->getById($id)];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function approve_request($id)
    {
        $request = Request::where('id', $id)->first();
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR && auth()->user()->company_id == $request->company_id) {
            $vacationAfterAccept = $this->requestRepository->getById($id);
            $vacationAfterAccept->status = RequestStatus::APPROVEED;
            $vacationAfterAccept->update();
            $user = User::where('id', $request->user_id)->first();
            if ($request->type == RequestType::VACATION && $request->vacation_type == VacationRequestTypes::DAILY && ($request->start_date == now()->format('Y-m-d') || $request->date == now()->format('Y-m-d'))) {
                $user->update([
                    'status' => EmployeeStatus::ON_VACATION
                ]);
            }
            if ($request->type == RequestType::VACATION && VacationRequestTypes::HOURLY && $request->payment_type == PaymentType::PAYMENT) {
                $availableTime = EmployeeAvailableTime::where('user_id', $user->id)->first();

                $start_time = $request->start_time;
                $end_time = $request->end_time;
                $start = new DateTime($start_time);
                $end = new DateTime($end_time);
                $diff = $start->diff($end);
                $hours = $diff->format('%h.%i');
                $availableTime->update([
                    'hourly_annual' => $availableTime->hourly_annual - $hours
                ]);
                // $currentDateTime = Carbon::now();
                // $latestAttendance = Attendance::where('user_id', $user->id)
                //     ->where('login_time', '!=', null)
                //     ->where('logout_time', null)
                //     ->where('status', 1)
                //     ->whereDate('date', $currentDateTime->toDateString())
                //     ->latest()
                //     ->first();
                // if ($latestAttendance) {
                //     $latestAttendance->update([
                //         'logout_time' => $request->end_time
                //     ]);
                //     $user->update([
                //         'status' => EmployeeStatus::ON_VACATION
                //     ]);
                // } else {
                //     $attendance  = new Attendance();
                //     $attendance->user_id = $user->id;
                //     $attendance->date = $request->start_date;
                //     $attendance->company_id = auth()->user()->company_id;
                //     $attendance->status = 1;
                //     $attendance->login_time = $request->start_time;
                //     $attendance->logout_time = $request->end_time;
                //     $attendance->save();
                // }
            }
            if ($request->type == RequestType::VACATION && VacationRequestTypes::DAILY && $request->payment_type == PaymentType::PAYMENT) {
                $availableTime = EmployeeAvailableTime::where('user_id', $user->id)->first();

                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $start = new DateTime($start_date);
                $end = new DateTime($end_date);
                $diff = $start->diff($end);
                $days = $diff->days;
                $availableTime->update([
                    'daily_annual' => $availableTime->daily_annual - $days
                ]);
            }
            if ($request->type == RequestType::RESIGNATION || $request->type == RequestType::RETIREMENT) {
                $user->update([
                    'status' => EmployeeStatus::PERMANENT_DISMISSED
                ]);
            }

            if ($request->type == RequestType::JUSTIFY) {
                $attendances = Attendance::where('user_id', $vacationAfterAccept->user_id)
                    ->where('status', 0)
                    ->where(function ($query) use ($vacationAfterAccept) {
                        $query->where(function ($subquery) use ($vacationAfterAccept) {
                            $subquery->whereNotNull('date')
                                ->whereDate('date', '=', $vacationAfterAccept->date)
                                ->orWhereBetween('date', [$vacationAfterAccept->start_date, $vacationAfterAccept->end_date]);
                        })
                            ->orWhere(function ($subquery) use ($vacationAfterAccept) {
                                $subquery->whereNull('date')
                                    ->whereBetween('date', [$vacationAfterAccept->start_date, $vacationAfterAccept->end_date]);
                            });
                    })
                    ->get();
                foreach ($attendances as $attendance) {
                    $attendance->update([
                        'is_justify' => 1
                    ]);
                }
            }

            return ['success' => true, 'data' => $vacationAfterAccept->load('user')];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }
    public function reject_request($data)
    {
        return $this->requestRepository->reject_request($data);
    }

    public function my_requests(RequestFilter $requestFilter = null)
    {
        if ($requestFilter != null) {
            return $this->requestRepository->my_requests($requestFilter);
        } else {
            return $this->requestRepository->paginate();
        }
    }
    public function my_approved_vacations_requests()
    {

        return $this->requestRepository->my_approved_vacations_requests();
    }

    public function vacation_requests()
    {

        return $this->requestRepository->vacation_requests();
    }
    public function justify_requests()
    {

        return $this->requestRepository->justify_requests();
    }
    public function retirement_requests()
    {

        return $this->requestRepository->retirement_requests();
    }
    public function resignation_requests()
    {
        return $this->requestRepository->resignation_requests();
    }
    public function getMonthlyData($filter)
    {
        return $this->getMonthlyShiftQuery->getMonthlyData($filter);
    }
    public function all_requests()
    {
        return $this->requestRepository->all_requests();
    }
    public function show_all_requests()
    {
        return $this->requestRepository->show_all_requests();
    }
}