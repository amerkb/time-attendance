<?php

namespace App\Services\Admin;

use App\Filter\Attendance\AttendanceFilter;
use App\Filter\Attendance\AttendanceOverviewFilter;
use App\Filter\Contract\ContractFilter;
use App\Filter\Employees\EmployeeFilter;
use App\Filter\Employees\LeaveCalendarFilter;
use App\Filter\Nationalalities\NationalFilter;
use App\Filter\Salary\SalaryFilter;
use App\Interfaces\Admin\AdminServiceInterface;
use App\Models\Alert;
use App\Models\Attendance;
use App\Models\DismissedReport;
use App\Models\EmployeeAvailableTime;
use App\Models\Holiday;
use App\Models\OverTimeAttendance;
use App\Models\User;
use App\Query\Admin\AdminDashboardQuery;
use App\Repository\Admin\AdminRepository;
use App\Statuses\EmployeeStatus;
use App\Statuses\UserTypes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminService implements AdminServiceInterface
{


    public function __construct(private AdminRepository $adminRepository, private AdminDashboardQuery $adminDashboardQuery)
    {
    }

    public function create_employee($data)
    {
        return $this->adminRepository->create_employee($data);
    }
    public function update_employee($data)
    {
        return $this->adminRepository->update_employee($data);
    }
    public function update_email($data)
    {
        return $this->adminRepository->update_email($data);
    }


    public function admin_update_employee($data)
    {
        return $this->adminRepository->admin_update_employee($data);
    }

    public function update_employee_permission_time($data)
    {
        return $this->adminRepository->update_employee_permission_time($data);
    }


    public function create_hr($data)
    {
        return $this->adminRepository->create_hr($data);
    }

    public function create_admin($data)
    {
        return $this->adminRepository->create_admin($data);
    }

    public function check_in_attendance($data)
    {
        return $this->adminRepository->check_in_attendance($data);
    }

    public function check_out_attendance($data)
    {
        return $this->adminRepository->check_out_attendance($data);
    }


    public function update_working_hours($data)
    {
        return $this->adminRepository->update_working_hours($data);
    }


    public function reward_adversaries_salary($data)
    {
        return $this->adminRepository->reward_adversaries_salary($data);
    }

    public function update_salary($data)
    {
        return $this->adminRepository->update_salary($data);
    }
    public function check_location($data)
    {
        return $this->adminRepository->check_location($data);
    }

    public function check_address($data)
    {
        return $this->adminRepository->check_address($data);
    }

    public function attendance_overview(AttendanceOverviewFilter $attendanceFilter = null)
    {
        if ($attendanceFilter != null)
            return $this->adminRepository->attendance_overview($attendanceFilter);
        else
            return $this->adminRepository->paginate();
    }

    public function renewal_employment_contract($data)
    {
        return $this->adminRepository->renewal_employment_contract($data);
    }

    public function cancle_employees_contract($data)
    {
        return $this->adminRepository->cancle_employees_contract($data);
    }

    public function deleteEmployee(int $id)
    {
        $user = User::where('id', $id)->where('company_id', auth()->user()->company_id)->first();
        if ($user) {
            if (auth()->user()->type == UserTypes::SUPER_ADMIN && $user->type == UserTypes::ADMIN) {
                $user->forceDelete();
                return response()->json(['message' => 'Admin Deleted Successfully'], 200);
            } elseif (auth()->user()->type == UserTypes::ADMIN && ($user->type == UserTypes::EMPLOYEE)) {
                $user->forceDelete();
                return response()->json(['message' => 'Employee Deleted Successfully'], 200);
            } elseif (auth()->user()->type == UserTypes::ADMIN && ($user->type == UserTypes::HR)) {
                $user->forceDelete();
                return response()->json(['message' => 'Hr Deleted Successfully'], 200);
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        } else {
            return response()->json(['message' => 'User Not Found'], 404);
        }
    }
    public function restore_employee(int $id)
    {
        $user = User::where('id', $id)->where('company_id', auth()->user()->company_id)->first();
        $dismissedReport = DismissedReport::where('user_id',  $user->id)
            ->where('elapsed_term_period', null)
            ->latest()
            ->first();
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {
            if ($user) {
                $user->update([
                    'status' => EmployeeStatus::UN_ACTIVE
                ]);
                if ($dismissedReport) {
                    $startDate = Carbon::parse($dismissedReport->start_date);
                    $currentDate = Carbon::now();
                    $elapsedTermPeriod = $startDate->diff($currentDate)->format('%m months, %d days');
                    $dismissedReport->update(['elapsed_term_period' => $elapsedTermPeriod]);
                }
                return response()->json(['message' => 'Employee Restored Successfully'], 200);
            } else {
                return response()->json(['message' => 'User Not Found'], 404);
            }
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    public function getDashboardData()
    {
        return $this->adminDashboardQuery->getDashboardData();
    }
    public function getHrsList()
    {
        return $this->adminRepository->getHrsList();
    }


    public function getEmployees(EmployeeFilter $employeeFilter = null)
    {
        if ($employeeFilter != null)
            return $this->adminRepository->getFilterItems($employeeFilter);
        else
            return $this->adminRepository->get();
    }
    public function getEmployeesDismissedList(EmployeeFilter $employeeFilter = null)
    {
        if ($employeeFilter != null)
            return $this->adminRepository->getEmployeesDismissedList($employeeFilter);
        else
            return $this->adminRepository->paginate();
    }


    public function employees_salaries(SalaryFilter $salaryFilter = null)
    {
        if ($salaryFilter != null)
            return $this->adminRepository->getSalaryFilterItems($salaryFilter);
        else
            return $this->adminRepository->paginate();
    }

    public function employees_attendances(AttendanceFilter $attendanceFilter = null)
    {
        if ($attendanceFilter != null)
            return $this->adminRepository->employees_attendances($attendanceFilter);
        else
            return $this->adminRepository->paginate();
    }

    public function get_contract_expiration(ContractFilter $contractFilter = null)
    {
        if ($contractFilter != null)
            return $this->adminRepository->get_contract_expiration($contractFilter);
        else
            return $this->adminRepository->paginate();
    }


    public function list_of_nationalities(NationalFilter $nationalFilter = null)
    {
        if ($nationalFilter != null)
            return $this->adminRepository->list_of_nationalities($nationalFilter);
        else
            return $this->adminRepository->get();
    }
    public function leave_calendar(LeaveCalendarFilter $leaveCalendarFilter = null)
    {
        if ($leaveCalendarFilter != null)
            return $this->adminRepository->leave_calendar($leaveCalendarFilter);
        else
            return $this->adminRepository->get();
    }


    public function showEmployee(int $id)
    {
        $employee = user::where('id', $id)->first();
        if (auth()->user()->company_id == $employee->company_id) {
            return ['success' => true, 'data' => $employee->load(['salaries', 'availableTime', 'requests', 'attendancesMonthly', 'nationalitie', 'deposits', 'shifts', 'leaves', 'dismissedReport'])];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function my_shifts()
    {
        return $this->adminRepository->my_shifts();
    }


    public function remining_vacation_hour_employee(int $id)
    {
        $user = User::where('id', $id)->first();
        if (auth()->user()->id == $user->id) {
            $record = EmployeeAvailableTime::where('user_id', $id)->first();

            return ['success' => true, 'data' => $record, 'code' => 200];
        } else {
            return ['success' => false, 'message' => "Unauthorized", 'code' => 401];
        }
    }
    public static function careteAttendance()
    {

        $today = Carbon::today()->format('Y-m-d');
        $userId = Auth::id();

        $existingAttendance = Attendance::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->exists();

        if (!$existingAttendance) {
            Attendance::create([
                'user_id' => $userId,
                'date' => $today,
                'status' => 1
            ]);
        }
    }

    public static function AttendancePercentage($id)
    {

        $startDate = date('Y-m-01');

        $endDate = date('Y-m-d');

        $totalDays = date_diff(date_create($startDate), date_create($endDate))->format('%a');

        $attendanceDays = DB::table('attendances')
            ->where('user_id', $id)
            ->where('status', 1)
            ->whereBetween('date', [$startDate, $endDate])
            ->count();
        if ($totalDays != 0) {
            $percentage = ($attendanceDays / $totalDays) * 100;
            return number_format($percentage);
        } else {
            return 0;
        }
    }

    public static function GetMyAlerts($email)
    {
        $alerts = Alert::where('email', $email)->get();
        if ($alerts) {
            return $alerts->load('user');
        } else {
            return null;
        }
    }

    public function profile()
    {
        return $this->adminRepository->profile();
    }

    public static function CalculateNumberOfWorkingHours($id)
    {
        $user = User::find($id);
        $user_shifts = $user->shifts;
        $total_working_hours = 0;

        if ($user_shifts != null) {
            foreach ($user_shifts as $shift) {
                $start_time = Carbon::parse($shift->start_time);
                $end_time = Carbon::parse($shift->end_time);
                $working_hours = $end_time->diffInMinutes($start_time);
                $total_working_hours += $working_hours;
            }
        }
        $hours = floor($total_working_hours / 60);
        $minutes = $total_working_hours % 60;

        return "$hours hour, $minutes minute";
    }
    public static function GenerateSalary($user, $salary)
    {

        $currentMonth = Carbon::now()->month;
        $currentMonthDate = Carbon::now()->format('Y-m');
        $numberOfDays = Carbon::now()->daysInMonth;

        $holidays = Holiday::where(function ($query) use ($currentMonth) {
            $query->orWhere(function ($query) use ($currentMonth) {
                $query->whereMonth('start_date', $currentMonth)
                    ->orWhereMonth('end_date', $currentMonth);
            });
        })->get();

        $filterDays = 0;
        foreach ($holidays as $holiday) {
            if ($holiday['start_date'] && $holiday['end_date']) {
                $startDate = Carbon::createFromFormat('Y-m-d', $holiday['start_date']);
                $endDate = Carbon::createFromFormat('Y-m-d', $holiday['end_date']);
                $diffDays = $endDate->diffInDays($startDate) + 1;
                $filterDays += $diffDays;
            }
        }

        $activeDays = $numberOfDays - $filterDays;

        $attendanceActiveMinutes = Attendance::where('user_id', $user->id)
            ->where(function ($query) use ($currentMonthDate) {
                $query->where(function ($subquery) use ($currentMonthDate) {
                    $subquery->where('status', 1)
                        ->where('date', 'like', $currentMonthDate . '%');
                })
                    ->orWhere(function ($subquery) use ($currentMonthDate) {
                        $subquery->where('status', 0)
                            ->where('is_justify', 1)
                            ->where('date', 'like', $currentMonthDate . '%');
                    });
            })
            ->get();

        $total_attendance_minutes = 0;

        if ($attendanceActiveMinutes != null) {
            foreach ($attendanceActiveMinutes as $attendanceActiveMinute) {
                $start_time = Carbon::parse($attendanceActiveMinute->login_time);
                $end_time = Carbon::parse($attendanceActiveMinute->logout_time);
                $working_minutes = $end_time->diffInMinutes($start_time);
                $total_attendance_minutes += $working_minutes;
            }
        }

        $salaryOfDay = $user->basic_salary / $activeDays;

        $user_shifts = $user->shifts;
        $total_working_minutes = 0;

        if ($user_shifts != null && count($user_shifts) > 0) {
            foreach ($user_shifts as $shift) {
                $start_time = Carbon::parse($shift->start_time);
                $end_time = Carbon::parse($shift->end_time);
                $working_hours = $end_time->diffInMinutes($start_time);
                $total_working_minutes += $working_hours;
            }
        }

        if ($total_working_minutes > 0) {
            $salaryOfMinutes = $salaryOfDay / $total_working_minutes;
        } else {
            $salaryOfMinutes = 0;
        }

        $firstSalary = $total_attendance_minutes * $salaryOfMinutes;

        $salaryRewerdsAdve = $salary - $user->basic_salary;

        $lastSalary = round($firstSalary + $salaryRewerdsAdve);

        return $lastSalary;
    }
    public static function NumberOfOverTime($user)
    {
        $currentMonthDate = Carbon::now()->format('Y-m');
        $attendanceActiveMinutes = OverTimeAttendance::where('user_id', $user->id)
            ->where(function ($query) use ($currentMonthDate) {
                $query->where(function ($subquery) use ($currentMonthDate) {
                    $subquery->where('status', 1)
                        ->where('date', 'like', $currentMonthDate . '%');
                });
            })
            ->get();

        $total_attendance_minutes = 0;

        if ($attendanceActiveMinutes != null) {
            foreach ($attendanceActiveMinutes as $attendanceActiveMinute) {
                $start_time = Carbon::parse($attendanceActiveMinute->login_time);
                $end_time = Carbon::parse($attendanceActiveMinute->logout_time);
                $working_minutes = $end_time->diffInMinutes($start_time);
                $total_attendance_minutes += $working_minutes;
            }
        }

        $hours = floor($total_attendance_minutes / 60);
        $minutes = $total_attendance_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return [$hours, $minutes];
        } else {
            return null;
        }
    }
    public static function DiscountReport($user)
    {
        $currentMonth = Carbon::now()->month;
        $currentMonthDate = Carbon::now()->format('Y-m');

        $holidays = Holiday::where(function ($query) use ($currentMonth) {
            $query->orWhere(function ($query) use ($currentMonth) {
                $query->whereMonth('start_date', $currentMonth)
                    ->orWhereMonth('end_date', $currentMonth);
            });
        })->get();

        $filterDays = 0;
        foreach ($holidays as $holiday) {
            if ($holiday['start_date'] && $holiday['end_date']) {
                $startDate = Carbon::createFromFormat('Y-m-d', $holiday['start_date']);
                $endDate = Carbon::createFromFormat('Y-m-d', $holiday['end_date']);
                $diffDays = $endDate->diffInDays($startDate) + 1;
                $filterDays += $diffDays;
            }
        }
        $attendanceActiveMinutes = Attendance::where('user_id', $user->id)
            ->where(function ($query) use ($currentMonthDate) {
                $query->where(function ($subquery) use ($currentMonthDate) {
                    $subquery->where('status', 1)
                        ->where('date', 'like', $currentMonthDate . '%');
                })
                    ->orWhere(function ($subquery) use ($currentMonthDate) {
                        $subquery->where('status', 0)
                            ->where('is_justify', 1)
                            ->where('date', 'like', $currentMonthDate . '%');
                    });
            })
            ->get();

        $total_attendance_minutes = 0;

        if ($attendanceActiveMinutes != null) {
            foreach ($attendanceActiveMinutes as $attendanceActiveMinute) {
                $start_time = Carbon::parse($attendanceActiveMinute->login_time);
                $end_time = Carbon::parse($attendanceActiveMinute->logout_time);
                $working_minutes = $end_time->diffInMinutes($start_time);
                $total_attendance_minutes += $working_minutes;
            }
        }
        $user_shifts = $user->shifts;
        $total_working_minutes = 0;

        if ($user_shifts != null && count($user_shifts) > 0) {
            foreach ($user_shifts as $shift) {
                $start_time = Carbon::parse($shift->start_time);
                $end_time = Carbon::parse($shift->end_time);
                $working_hours = $end_time->diffInMinutes($start_time);
                $total_working_minutes += $working_hours;
            }
        }
        if ($total_attendance_minutes > 0 && $total_working_minutes - $total_attendance_minutes > 0) {
            $result =   $total_working_minutes - $total_attendance_minutes;
            $hours = floor($result / 60);
            $minutes = $result % 60;
            return [$hours, $minutes];
        } elseif ($total_attendance_minutes > 0 && $total_working_minutes - $total_attendance_minutes == 0) {
            return null;
        } elseif ($total_attendance_minutes == 0) {
            return null;
        }
    }
}
