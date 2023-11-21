<?php

namespace App\Repository\Admin;

use App\Filter\Attendance\AttendanceFilter;
use App\Filter\Attendance\AttendanceOverviewFilter;
use App\Filter\Employees\EmployeeFilter;
use App\Filter\Employees\LeaveCalendarFilter;
use App\Filter\Salary\SalaryFilter;
use App\Http\Trait\UploadImage;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Contract;
use App\Models\DismissedReport;
use App\Models\EmployeeAvailableTime;
use App\Models\Holiday;
use App\Models\Location;
use App\Models\Nationalitie;
use App\Models\Notification;
use App\Models\OverTimeAttendance;
use App\Models\Percentage;
use App\Models\Request;
use App\Models\Salary;
use App\Models\Shift;
use App\Models\User;
use App\Repository\BaseRepositoryImplementation;
use App\Services\Email\EmailService;
use App\Services\Notifications\NotificationService;
use App\Statuses\AdversariesType;
use App\Statuses\DepositStatus;
use App\Statuses\EmployeeStatus;
use App\Statuses\HolidayTypes;
use App\Statuses\NotificationType;
use App\Statuses\RequestStatus;
use App\Statuses\RequestType;
use App\Statuses\RewardsType;
use App\Statuses\TerminateTime;
use App\Statuses\TerminateType;
use App\Statuses\UserTypes;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminRepository extends BaseRepositoryImplementation
{
    use UploadImage;
    public function getFilterItems($filter)
    {
        $records = User::query()->whereIn('type', [UserTypes::EMPLOYEE, UserTypes::HR])->where('company_id', auth()->user()->company_id)->whereNotIn('status', [EmployeeStatus::TEMPORARY_DISMISSED, EmployeeStatus::PERMANENT_DISMISSED])->with('dismissedReport', 'leaves');
        if ($filter instanceof EmployeeFilter) {

            $records->when(isset($filter->name), function ($records) use ($filter) {
                $records->where('name', 'LIKE', '%' . $filter->getName() . '%');
            });

            $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                $records->orderBy($filter->getOrderBy(), $filter->getOrder());
            });


            return $records->get();
        }
        return $records->get();
    }

    public function getEmployeesDismissedList($filter)
    {
        $records = User::query()->whereIn('type', [UserTypes::EMPLOYEE, UserTypes::HR])->where('company_id', auth()->user()->company_id)->whereIn('status', [EmployeeStatus::TEMPORARY_DISMISSED, EmployeeStatus::PERMANENT_DISMISSED])->with('dismissedReport', 'leaves');
        if ($filter instanceof EmployeeFilter) {
            $records->when(isset($filter->name), function ($records) use ($filter) {
                $records->where('name', 'LIKE', '%' . $filter->getName() . '%');
            });
            $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                $records->orderBy($filter->getOrderBy(), $filter->getOrder());
            });
            return $records->get();
        }
        return $records->get();
    }

    public function getSalaryFilterItems($filter)
    {
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {
            $records = Salary::query()->where('company_id', auth()->user()->company_id);
            if ($filter instanceof SalaryFilter) {

                $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                    $records->orderBy($filter->getOrderBy(), $filter->getOrder());
                });

                $salaries = $records->with('user')->whereHas('user', function ($query) {
                    $query->whereNotIn('status', [EmployeeStatus::TEMPORARY_DISMISSED, EmployeeStatus::PERMANENT_DISMISSED]);
                })->get();
                return ['success' => true, 'data' => $salaries];
            }
            $salaries = $records->with('user')->whereHas('user', function ($query) {
                $query->whereNotIn('status', [EmployeeStatus::TEMPORARY_DISMISSED, EmployeeStatus::PERMANENT_DISMISSED]);
            })->get();
            return ['success' => true, 'data' => $salaries];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function list_of_nationalities($filter)
    {
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {
            $records = Nationalitie::query();
            return $records->get();
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function my_shifts()
    {
        $records = Shift::query()->where('user_id', auth()->user()->id)->where('company_id', auth()->user()->company_id);

        return ['success' => true, 'data' => $records->get()];
    }
    public function leave_calendar($filter)
    {
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {
            $company_id = auth()->user()->company_id;

            $records = Request::query()
                ->where('company_id', $company_id)
                ->Where('type', RequestType::VACATION)
                ->where('status', RequestStatus::APPROVEED)
                ->with('user');
            if ($filter instanceof LeaveCalendarFilter) {
                $records->when(isset($filter->date), function ($records) use ($filter) {
                    $date = $filter->getDate();
                    $records->where(function ($query) use ($date) {
                        $query->whereDate('date', $date)
                            ->orWhereDate('start_date', '<=', $date)
                            ->whereDate('end_date', '>=', $date);
                    });
                });

                $dayType = $filter->getDay();
                if ($dayType == 1) {
                    $records->where(function ($query) {
                        $query->whereDate('date', now()->toDateString())
                            ->orWhere(function ($query) {
                                $query->whereDate('start_date', '<=', now()->toDateString())
                                    ->whereDate('end_date', '>=', now()->toDateString());
                            });
                    });
                } else if ($dayType == 2) {
                    $records->where(function ($query) {
                        $query->whereDate('date', now()->addDay()->toDateString())
                            ->orWhere(function ($query) {
                                $query->whereDate('start_date', '<=', now()->addDay()->toDateString())
                                    ->whereDate('end_date', '>=', now()->addDay()->toDateString());
                            });
                    });
                }

                return ['success' => true, 'data' => $records->get()];
            }
            return ['success' => true, 'data' => $records->get()];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }


    public function employees_attendances($filter)
    {
        if (auth()->user()->type == UserTypes::ADMIN) {

            $records = User::query()->where('type', UserTypes::HR)->where('company_id', auth()->user()->company_id);
            $records = Attendance::query()->whereMonth('date', Carbon::now()->month);
            if ($filter instanceof AttendanceFilter) {

                $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                    $records->orderBy($filter->getOrderBy(), $filter->getOrder());
                });


                $attendances = $records->with('user')->whereHas('user', function ($query) {
                    $query->whereNotIn('status', [EmployeeStatus::TEMPORARY_DISMISSED, EmployeeStatus::PERMANENT_DISMISSED]);
                })->get();


                return ['success' => true, 'data' => $attendances];
            }
            $attendances = $records->with('user')->whereHas('user', function ($query) {
                $query->whereNotIn('status', [EmployeeStatus::TEMPORARY_DISMISSED, EmployeeStatus::PERMANENT_DISMISSED]);
            })->get();
            return ['success' => true, 'data' => $attendances];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }
    public function get_contract_expiration($filter)
    {
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {
            $records = Contract::where('end_contract_date', '<=', Carbon::now()->addMonth())
                ->where('company_id', auth()->user()->company_id)
                ->whereNotNull('end_contract_date')
                ->whereHas('user', function ($query) {
                    $query->whereNotIn('status', [EmployeeStatus::TEMPORARY_DISMISSED, EmployeeStatus::PERMANENT_DISMISSED]);
                })
                ->get();
            return ['success' => true, 'data' => $records->load('user')];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }
    public function getHrsList()
    {
        $records = User::query()->where('type', UserTypes::HR)->where('company_id', auth()->user()->company_id);
        return $records->get();
    }

    public function create_employee($data)
    {
        DB::beginTransaction();
        try {
            if (auth()->user()->type == UserTypes::HR || auth()->user()->type == UserTypes::ADMIN) {
                $user = new User();
                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->password = Hash::make($data['password']);
                $user->reset_password = $data['password'];
                $user->departement = $data['departement'];
                $user->skills = $data['skills'];
                $user->gender = $data['gender'];
                $user->status = EmployeeStatus::UN_ACTIVE;
                $user->phone = $data['phone'];
                $user->company_id = auth()->user()->company_id;
                $user->serial_number = $data['serial_number'];
                $user->work_email = $data['work_email'];
                $user->mobile = $data['mobile'];
                $user->nationalitie_id = $data['nationalitie_id'];
                $user->birthday_date = $data['birthday_date'];
                $user->material_status = $data['material_status'];
                $user->address = $data['address'];
                $user->guarantor = $data['guarantor'];
                $user->branch = $data['branch'];
                $user->start_job_contract = $data['start_job_contract'];
                $user->end_job_contract = $data['end_job_contract'];

                if (isset($data['position']) && $data['position'] !== null && $data['position'] !== '') {
                    $user->position = $data['position'];
                }

                if (Arr::has($data, 'image')) {
                    $file = Arr::get($data, 'image');
                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->image = $file_name;
                    }
                }

                if (Arr::has($data, 'id_photo')) {
                    $file = Arr::get($data, 'id_photo');
                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->id_photo = $file_name;
                    }
                }

                if (Arr::has($data, 'biography')) {
                    $file = Arr::get($data, 'biography');
                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->biography = $file_name;
                    }
                }

                if (Arr::has($data, 'visa')) {
                    $file = Arr::get($data, 'visa');
                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->visa = $file_name;
                    }
                }

                if (Arr::has($data, 'municipal_card')) {
                    $file = Arr::get($data, 'municipal_card');
                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->municipal_card = $file_name;
                    }
                }

                if (Arr::has($data, 'health_insurance')) {
                    $file = Arr::get($data, 'health_insurance');
                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->health_insurance = $file_name;
                    }
                }

                if (Arr::has($data, 'passport')) {
                    $file = Arr::get($data, 'passport');
                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->passport = $file_name;
                    }
                }

                if (Arr::has($data, 'employee_sponsorship')) {
                    $file = Arr::get($data, 'employee_sponsorship');
                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->employee_sponsorship = $file_name;
                    }
                }

                if (Arr::has($data, 'employee_residence')) {
                    $file = Arr::get($data, 'employee_residence');
                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->employee_residence = $file_name;
                    }
                }

                if (isset($data['end_visa']) && $data['end_visa'] !== null && $data['end_visa'] != '') {
                    $user->end_visa = $data['end_visa'];
                }
                if (isset($data['end_passport'])  && $data['end_passport'] !== null && $data['end_passport'] != '') {
                    $user->end_passport = $data['end_passport'];
                }
                if (isset($data['end_employee_sponsorship'])  && $data['end_employee_sponsorship'] !== null && $data['end_employee_sponsorship'] != '') {
                    $user->end_employee_sponsorship = $data['end_employee_sponsorship'];
                }
                if (isset($data['end_municipal_card']) && $data['end_municipal_card'] !== null && $data['end_municipal_card'] != '') {
                    $user->end_municipal_card = $data['end_municipal_card'];
                }
                if (isset($data['end_health_insurance']) && $data['end_health_insurance'] !== null && $data['end_health_insurance'] != '') {
                    $user->end_health_insurance = $data['end_health_insurance'];
                }
                if (isset($data['end_employee_residence']) && $data['end_employee_residence'] !== null && $data['end_employee_residence'] != '') {
                    $user->end_employee_residence = $data['end_employee_residence'];
                }
                $user->basic_salary = $data['basic_salary'];
                $user->type = UserTypes::EMPLOYEE;
                if (isset($data['entry_time']) && $data['entry_time'] !== null && $data['entry_time'] != '') {
                    $user->entry_time = $data['entry_time'];
                }

                if (isset($data['leave_time']) && $data['leave_time'] !== null && $data['leave_time'] != '') {
                    $user->leave_time = $data['leave_time'];
                }
                $user->save();
                $salary = Salary::create([
                    'user_id' => $user->id,
                    'salary' => $user->basic_salary,
                    'basic_salary' => $user->basic_salary,
                    'rewards' => 0,
                    'adversaries' => 0,
                    'housing_allowance' => 0,
                    'transportation_allowance' => 0,
                    'company_id' => auth()->user()->company_id,
                    'date' => date('Y-m-d'),
                ]);
                if (isset($data['housing_allowance']) && isset($data['transportation_allowance'])) {
                    $salary->update([
                        'transportation_allowance' => $data['transportation_allowance'],
                        'housing_allowance' => $data['housing_allowance'],
                        'salary' =>  $salary->salary + $data['transportation_allowance'] + $data['housing_allowance'],
                        'date' => date('Y-m-d'),
                    ]);
                } elseif (isset($data['housing_allowance'])) {
                    $salary->update([
                        'housing_allowance' => $data['housing_allowance'],
                        'salary' =>  $salary->salary + $data['housing_allowance'],
                        'date' => date('Y-m-d'),
                    ]);
                } elseif (isset($data['transportation_allowance'])) {
                    $salary->update([
                        'transportation_allowance' => $data['transportation_allowance'],
                        'salary' =>  $salary->salary + $data['transportation_allowance'],
                        'date' => date('Y-m-d'),
                    ]);
                }
                Contract::create([
                    'user_id' => $user->id,
                    'company_id' => $user->company_id,
                    'start_contract_date' => $data['start_job_contract'],
                    'end_contract_date' =>  $data['end_job_contract'],
                ]);
                if ((isset($data['number_of_shifts'])) && !empty($data['shifts'])) {
                    foreach ($data['shifts'] as $shift) {
                        Shift::create([
                            'user_id' => $user->id,
                            'company_id' => $user->company_id,
                            'start_time' => $shift['start_time'],
                            'end_time' => $shift['end_time'],
                            'start_break_hour' => $shift['start_break_hour'],
                            'end_break_hour' => $shift['end_break_hour'],
                        ]);
                    }
                }
                if (isset($data['hourly_annual']) && isset($data['daily_annual'])) {
                    $employeeAvailableTime = new EmployeeAvailableTime();
                    $employeeAvailableTime->user_id = $user->id;
                    $employeeAvailableTime->hourly_annual = $data['hourly_annual'];
                    $employeeAvailableTime->daily_annual = $data['daily_annual'];
                    $employeeAvailableTime->old_hourly_annual = $data['hourly_annual'];
                    $employeeAvailableTime->old_daily_annual = $data['daily_annual'];
                    $employeeAvailableTime->company_id = $user->company_id;
                    $employeeAvailableTime->save();
                } elseif (isset($data['hourly_annual'])) {
                    $employeeAvailableTime = new EmployeeAvailableTime();
                    $employeeAvailableTime->user_id = $user->id;
                    $employeeAvailableTime->hourly_annual = $data['hourly_annual'];
                    $employeeAvailableTime->old_hourly_annual = $data['hourly_annual'];
                    $employeeAvailableTime->company_id = $user->company_id;
                    $employeeAvailableTime->save();
                } elseif (isset($data['daily_annual'])) {
                    $employeeAvailableTime = new EmployeeAvailableTime();
                    $employeeAvailableTime->user_id = $user->id;
                    $employeeAvailableTime->daily_annual = $data['daily_annual'];
                    $employeeAvailableTime->old_daily_annual = $data['daily_annual'];
                    $employeeAvailableTime->company_id = $user->company_id;
                    $employeeAvailableTime->save();
                }
                $user->generate_code();
                $userCode = $user->code;
                $userEmail = $user->email;
                $userPassword = $data['password'];
                $recipientEmail = $userEmail;
                $company = Company::where('id', auth()->user()->company_id)->first();
                $companyName = $company->name;
                EmailService::sendHtmlEmail($recipientEmail, $companyName, $userCode, $userEmail, $userPassword);
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }

            DB::commit();

            return ['success' => true, 'data' => $user->load('nationalitie', 'shifts', 'deposits', 'leaves')];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function update_employee($data)
    {
        DB::beginTransaction();
        $user = $this->getById(auth()->user()->id);
        try {
            if (auth()->user()->type == UserTypes::EMPLOYEE || auth()->user()->id == $user->id) {
                $newData = $this->updateById(auth()->user()->id, $data);
                if (Arr::has($data, 'image')) {
                    $file = Arr::get($data, 'image');
                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->deleteAndUploadEmployeeAttachment($file, $user->id, 'image');
                        $user->image = $file_name;
                    }
                }
                $newData->save();
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
            DB::commit();
            if ($newData === null) {
                return ['success' => false, 'message' => "User was not Updated"];
            }
            return ['success' => true, 'data' => $newData->load('nationalitie', 'shifts', 'deposits', 'leaves')];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function update_email($data)
    {

        DB::beginTransaction();
        $user = user::where('id', auth()->user()->id)->first();

        try {
            if (auth()->user()->id == $user->id) {
                $user->update([
                    'temporary_email' => $data['email'],
                    'is_verifed' => false,
                ]);
                $user->generate_code();
                $userCode = $user->code;
                $userEmail = $user->temporary_email;
                $userPassword = $user->reset_password;
                $recipientEmail = $userEmail;
                $company = Company::where('id', auth()->user()->company_id)->first();
                $companyName = $company->name;
                EmailService::sendHtmlEmail($recipientEmail, $companyName, $userCode, $userEmail, $userPassword);
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
            DB::commit();

            return ['success' => true, 'data' => $user->load('nationalitie', 'shifts', 'deposits', 'leaves')];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function admin_update_employee($data)
    {
        DB::beginTransaction();

        $currentMonth = Carbon::now()->format('Y-m');

        $salary = Salary::where('user_id', $data['user_id'])->whereDate('created_at', 'like', $currentMonth . '%')->first();
        try {
            if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {
                $user = $this->updateById($data['user_id'], $data);
                if (isset($data['housing_allowance']) && isset($data['transportation_allowance'])) {
                    $salary->update([
                        'transportation_allowance' => $data['transportation_allowance'],
                        'housing_allowance' => $data['housing_allowance'],
                        'salary' =>  $salary->salary + $data['transportation_allowance'] + $data['housing_allowance'],
                        'date' => date('Y-m-d'),
                    ]);
                } elseif (isset($data['housing_allowance'])) {
                    $salary->update([
                        'housing_allowance' => $data['housing_allowance'],
                        'salary' =>  $salary->salary + $data['housing_allowance'],
                        'date' => date('Y-m-d'),
                    ]);
                } elseif (isset($data['transportation_allowance'])) {
                    $salary->update([
                        'transportation_allowance' => $data['transportation_allowance'],
                        'salary' =>  $salary->salary + $data['transportation_allowance'],
                        'date' => date('Y-m-d'),
                    ]);
                }

                if (Arr::has($data, 'id_photo')) {
                    $file = Arr::get($data, 'id_photo');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->deleteAndUploadEmployeeAttachment($file, $user->id, 'id_photo');
                        $user->id_photo = $file_name;
                    }
                }
                if (Arr::has($data, 'biography')) {
                    $file = Arr::get($data, 'biography');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->deleteAndUploadEmployeeAttachment($file, $user->id, 'biography');
                        $user->biography = $file_name;
                    }
                }


                if (Arr::has($data, 'visa')) {
                    $file = Arr::get($data, 'visa');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->deleteAndUploadEmployeeAttachment($file, $user->id, 'visa');
                        $user->visa = $file_name;
                    }
                }

                if (Arr::has($data, 'municipal_card')) {
                    $file = Arr::get($data, 'municipal_card');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->deleteAndUploadEmployeeAttachment($file, $user->id, 'municipal_card');
                        $user->municipal_card = $file_name;
                    }
                }


                if (Arr::has($data, 'health_insurance')) {
                    $file = Arr::get($data, 'health_insurance');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->deleteAndUploadEmployeeAttachment($file, $user->id, 'health_insurance');
                        $user->health_insurance = $file_name;
                    }
                }

                if (Arr::has($data, 'passport')) {
                    $file = Arr::get($data, 'passport');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->deleteAndUploadEmployeeAttachment($file, $user->id, 'passport');
                        $user->passport = $file_name;
                    }
                }


                if (Arr::has($data, 'employee_sponsorship')) {
                    $file = Arr::get($data, 'employee_sponsorship');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->deleteAndUploadEmployeeAttachment($file, $user->id, 'employee_sponsorship');
                        $user->employee_sponsorship = $file_name;
                    }
                }

                if (Arr::has($data, 'employee_residence')) {
                    $file = Arr::get($data, 'employee_residence');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->deleteAndUploadEmployeeAttachment($file, $user->id, 'employee_residence');
                        $user->employee_residence = $file_name;
                    }
                }
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
            DB::commit();
            if ($user === null) {
                return ['success' => false, 'message' => "User was not Updated"];
            }
            return ['success' => true, 'data' => $user->load('salaries', 'leaves')];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function update_employee_permission_time($data)
    {
        DB::beginTransaction();
        $user = $this->getById($data['user_id']);

        try {

            if (auth()->user()->type == UserTypes::HR || (auth()->user()->type == UserTypes::ADMIN && auth()->user()->company_id == $user->company_id)) {
                if (isset($data['entry_time']) && !isset($data['leave_time'])) {
                    $user->update([
                        'entry_time' => $data['entry_time']
                    ]);
                } elseif (isset($data['leave_time']) && !isset($data['entry_time'])) {
                    $user->update([
                        'leave_time' => $data['leave_time']
                    ]);
                } elseif (isset($data['leave_time']) && isset($data['entry_time'])) {
                    return ['success' => false, 'message' => "Choose Entry Time Or Leave Time Not Together"];
                }
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
            DB::commit();

            if ($user === null) {
                return ['success' => false, 'message' => "User was not Updated"];
            }

            return ['success' => true, 'data' => $user];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function create_hr($data)
    {
        DB::beginTransaction();
        try {
            if (auth()->user()->type == UserTypes::ADMIN) {
                $user = new User();
                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->password = Hash::make($data['password']);
                $user->reset_password = $data['password'];
                $user->departement = $data['departement'];
                $user->skills = $data['skills'];
                $user->gender = $data['gender'];
                $user->status = EmployeeStatus::UN_ACTIVE;
                $user->phone = $data['phone'];
                $user->serial_number = $data['serial_number'];
                $user->work_email = $data['work_email'];
                $user->mobile = $data['mobile'];
                $user->nationalitie_id = $data['nationalitie_id'];
                $user->birthday_date = $data['birthday_date'];
                $user->material_status = $data['material_status'];
                $user->address = $data['address'];
                $user->guarantor = $data['guarantor'];
                $user->branch = $data['branch'];
                $user->start_job_contract = $data['start_job_contract'];
                $user->end_job_contract = $data['end_job_contract'];


                if (Arr::has($data, 'image')) {
                    $file = Arr::get($data, 'image');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->image = $file_name;
                    }
                }

                if (Arr::has($data, 'id_photo')) {
                    $file = Arr::get($data, 'id_photo');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->id_photo = $file_name;
                    }
                }


                if (Arr::has($data, 'biography')) {
                    $file = Arr::get($data, 'biography');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->biography = $file_name;
                    }
                }

                if (Arr::has($data, 'visa')) {
                    $file = Arr::get($data, 'visa');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->visa = $file_name;
                    }
                }

                if (Arr::has($data, 'municipal_card')) {
                    $file = Arr::get($data, 'municipal_card');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->municipal_card = $file_name;
                    }
                }

                if (Arr::has($data, 'health_insurance')) {
                    $file = Arr::get($data, 'health_insurance');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->health_insurance = $file_name;
                    }
                }

                if (Arr::has($data, 'passport')) {
                    $file = Arr::get($data, 'passport');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->passport = $file_name;
                    }
                }

                if (Arr::has($data, 'employee_sponsorship')) {
                    $file = Arr::get($data, 'employee_sponsorship');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->employee_sponsorship = $file_name;
                    }
                }

                if (Arr::has($data, 'employee_residence')) {
                    $file = Arr::get($data, 'employee_residence');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->employee_residence = $file_name;
                    }
                }

                if (isset($data['end_visa'])) {
                    $user->end_visa = $data['end_visa'];
                }

                if (isset($data['end_passport'])) {
                    $user->end_passport = $data['end_passport'];
                }

                if (isset($data['end_employee_sponsorship'])) {
                    $user->end_employee_sponsorship = $data['end_employee_sponsorship'];
                }

                if (isset($data['end_municipal_card'])) {
                    $user->end_municipal_card = $data['end_municipal_card'];
                }

                if (isset($data['end_health_insurance'])) {
                    $user->end_health_insurance = $data['end_health_insurance'];
                }

                if (isset($data['end_employee_residence'])) {
                    $user->end_employee_residence = $data['end_employee_residence'];
                }
                $user->basic_salary = $data['basic_salary'];
                $user->company_id = auth()->user()->company_id;
                $user->type = UserTypes::HR;
                $user->save();
                $salary = Salary::create([
                    'user_id' => $user->id,
                    'salary' => $user->basic_salary,
                    'basic_salary' => $user->basic_salary,
                    'rewards' => 0,
                    'adversaries' => 0,
                    'housing_allowance' => 0,
                    'transportation_allowance' => 0,
                    'company_id' => auth()->user()->company_id,
                    'date' => date('Y-m-d'),
                ]);
                if (isset($data['housing_allowance']) && isset($data['transportation_allowance'])) {
                    $salary->update([
                        'transportation_allowance' => $data['transportation_allowance'],
                        'housing_allowance' => $data['housing_allowance'],
                        'salary' =>  $salary->salary + $data['transportation_allowance'] + $data['housing_allowance'],
                        'date' => date('Y-m-d'),
                    ]);
                } elseif (isset($data['housing_allowance'])) {
                    $salary->update([
                        'housing_allowance' => $data['housing_allowance'],
                        'salary' =>  $salary->salary + $data['housing_allowance'],
                        'date' => date('Y-m-d'),
                    ]);
                } elseif (isset($data['transportation_allowance'])) {
                    $salary->update([
                        'transportation_allowance' => $data['transportation_allowance'],
                        'salary' =>  $salary->salary + $data['transportation_allowance'],
                        'date' => date('Y-m-d'),
                    ]);
                }
                Contract::create([
                    'user_id' => $user->id,
                    'company_id' => $user->company_id,
                    'start_contract_date' => $data['start_job_contract'],
                    'end_contract_date' =>  $data['end_job_contract'],
                ]);
                if ((isset($data['number_of_shifts'])) && !empty($data['shifts'])) {
                    foreach ($data['shifts'] as $shift) {
                        Shift::create([
                            'user_id' => $user->id,
                            'company_id' => $user->company_id,
                            'start_time' => $shift['start_time'],
                            'end_time' => $shift['end_time'],
                            'start_break_hour' => $shift['start_break_hour'],
                            'end_break_hour' => $shift['end_break_hour'],
                        ]);
                    }
                }
                if (isset($data['hourly_annual']) && isset($data['daily_annual'])) {
                    $employeeAvailableTime = new EmployeeAvailableTime();
                    $employeeAvailableTime->user_id = $user->id;
                    $employeeAvailableTime->hourly_annual = $data['hourly_annual'];
                    $employeeAvailableTime->daily_annual = $data['daily_annual'];
                    $employeeAvailableTime->old_hourly_annual = $data['hourly_annual'];
                    $employeeAvailableTime->old_daily_annual = $data['daily_annual'];
                    $employeeAvailableTime->company_id = $user->company_id;
                    $employeeAvailableTime->save();
                } elseif (isset($data['hourly_annual'])) {
                    $employeeAvailableTime = new EmployeeAvailableTime();
                    $employeeAvailableTime->user_id = $user->id;
                    $employeeAvailableTime->hourly_annual = $data['hourly_annual'];
                    $employeeAvailableTime->old_hourly_annual = $data['hourly_annual'];
                    $employeeAvailableTime->company_id = $user->company_id;
                    $employeeAvailableTime->save();
                } elseif (isset($data['daily_annual'])) {
                    $employeeAvailableTime = new EmployeeAvailableTime();
                    $employeeAvailableTime->user_id = $user->id;
                    $employeeAvailableTime->daily_annual = $data['daily_annual'];
                    $employeeAvailableTime->old_daily_annual = $data['daily_annual'];
                    $employeeAvailableTime->company_id = $user->company_id;
                    $employeeAvailableTime->save();
                }
                $user->generate_code();
                $userCode = $user->code;
                $userEmail = $user->email;
                $userPassword = $data['password'];
                $recipientEmail = $userEmail;
                $company = Company::where('id', auth()->user()->company_id)->first();
                $companyName = $company->name;
                EmailService::sendHtmlEmail($recipientEmail, $companyName, $userCode, $userEmail, $userPassword);
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }

            DB::commit();

            if ($user === null) {
                return ['success' => false, 'message' => "User was not created"];
            }

            return ['success' => true, 'data' => $user];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function create_admin($data)
    {
        DB::beginTransaction();
        try {
            if (auth()->user()->type == UserTypes::SUPER_ADMIN) {
                $user = new User();
                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->password = Hash::make($data['password']);
                $user->reset_password = $data['password'];
                $user->gender = $data['gender'];
                $user->phone = $data['phone'];
                $user->company_id = $data['company_id'];
                $user->serial_number = $data['serial_number'];
                $user->work_email = $data['work_email'];
                $user->mobile = $data['mobile'];
                $user->nationalitie_id = $data['nationalitie_id'];
                $user->birthday_date = $data['birthday_date'];
                $user->material_status = $data['material_status'];
                $user->address = $data['address'];
                $user->branch = $data['branch'];
                $user->type = UserTypes::ADMIN;

                if (Arr::has($data, 'image')) {
                    $file = Arr::get($data, 'image');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->uploadEmployeeAttachment($file, $user->id);
                        $user->image = $file_name;
                    }
                }

                $user->save();
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }

            DB::commit();

            if ($user === null) {
                return ['success' => false, 'message' => "User was not created"];
            }

            return ['success' => true, 'data' => $user];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function update_salary($data)
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($data['user_id']);
            if ($user->type == UserTypes::HR && auth()->user()->company_id == $user->company_id && auth()->user()->type == UserTypes::ADMIN) {
                $user->update([
                    'next_month_salary' => $data['new_salary'],
                ]);
            } elseif ($user->type == UserTypes::EMPLOYEE && auth()->user()->company_id == $user->company_id && (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR)) {
                $user->update([
                    'next_month_salary' => $data['new_salary'],
                ]);
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
            DB::commit();
            return ['success' => true, 'data' => $user];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function renewal_employment_contract($data)
    {
        $user = User::findOrFail($data['user_id']);
        $contract = Contract::where('user_id', $data['user_id'])->first();

        DB::beginTransaction();
        try {
            if (auth()->user()->type == UserTypes::HR || auth()->user()->type == UserTypes::ADMIN && auth()->user()->company_id == $user->company_id) {
                $date = \Carbon\Carbon::create($user->end_job_contract);

                $newDateThreeMonth = $date->copy()->addMonths(3)->format('Y-m-d');

                $newDateSixMonth = $date->copy()->addMonths(6)->format('Y-m-d');

                $newDateYear = $date->copy()->addMonths(12)->format('Y-m-d');

                if (isset($data['new_date']) && isset($data['number_of_month'])) {
                    return ['success' => false, 'message' => "Please Choose New Date Or Number Of Month Not Together"];
                }

                if (isset($data['new_date'])) {

                    $user->update([
                        'end_job_contract' => $data['new_date'],
                    ]);
                    $contract->update([
                        'end_contract_date' => $data['new_date'],
                    ]);
                } elseif (isset($data['number_of_month']) && $data['number_of_month'] == TerminateTime::THREE_MONTH) {

                    $contract->update([
                        'end_contract_date' => $newDateThreeMonth,
                        'previous_terminate_period' => $data['number_of_month'],
                    ]);
                    $user->update([
                        'end_job_contract' => $newDateThreeMonth,
                    ]);
                } elseif (isset($data['number_of_month']) && $data['number_of_month'] == TerminateTime::SIX_MONTH) {
                    $contract->update([
                        'end_contract_date' => $newDateSixMonth,
                        'previous_terminate_period' => $data['number_of_month'],
                    ]);
                    $user->update([
                        'end_job_contract' => $newDateSixMonth,
                    ]);
                } elseif (isset($data['number_of_month']) && $data['number_of_month'] == TerminateTime::ONE_YEAR) {


                    $contract->update([
                        'end_contract_date' => $newDateYear,
                        'previous_terminate_period' => $data['number_of_month'],
                    ]);
                    $user->update([
                        'end_job_contract' => $newDateYear,
                    ]);
                }
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
            DB::commit();
            return ['success' => true, 'data' => $contract->load('user')];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function cancle_employees_contract($data)
    {

        $user = User::findOrFail($data['user_id']);
        $contract = Contract::where('user_id', $data['user_id'])->first();


        $hasUnpaidDeposits = $user->deposits()->where('status', DepositStatus::UN_PAID)
            ->orWhere('extra_status', DepositStatus::UN_PAID_REJECTED)
            ->whereHas('user', function ($query) use ($user) {
                $query->where('id', $user->id);
            })->exists();

        DB::beginTransaction();
        try {
            if (auth()->user()->type == UserTypes::HR || auth()->user()->type == UserTypes::ADMIN && auth()->user()->company_id == $user->company_id) {

                if (!$hasUnpaidDeposits) {
                    $tomorrow = \Carbon\Carbon::tomorrow();
                    $date = \Carbon\Carbon::create($tomorrow);

                    $newDateThreeMonth = $date->copy()->addMonths(3)->format('Y-m-d');
                    $newDateThreeMonthString = \Carbon\Carbon::createFromFormat('Y-m-d', $newDateThreeMonth);


                    $newDateSixMonth = $date->copy()->addMonths(6)->format('Y-m-d');
                    $newDateSixMonthString = \Carbon\Carbon::createFromFormat('Y-m-d', $newDateSixMonth);

                    $newDateYear = $date->copy()->addMonths(12)->format('Y-m-d');
                    $newDateYearString = \Carbon\Carbon::createFromFormat('Y-m-d', $newDateYear);


                    $diffThreeMonths = $newDateThreeMonthString->diff($date)->format('%m months, %d days');
                    $diffSixMonths = $newDateSixMonthString->diff($date)->format('%m months, %d days');
                    $diffYears = $newDateYearString->diff($date)->format('%y years,%m months, %d days');
                    $dismissedReport = DismissedReport::where('user_id', $data['user_id'])
                        ->whereIn('end_date', [$newDateThreeMonth, $newDateSixMonth, $diffYears])
                        ->where('elapsed_term_period',  null)
                        ->latest()
                        ->first();

                    if ($data['terminate_type'] == TerminateType::TEMPORARY) {
                        if (isset($data['contract_termination_period']) && $data['contract_termination_period'] == TerminateTime::THREE_MONTH) {
                            $contract->update([
                                'end_contract_date' => $newDateThreeMonth,
                                "contract_termination_date" => date('Y-m-d'),
                                "contract_termination_period" => $diffThreeMonths,
                                "contract_termination_reason" => $data['contract_termination_reason'],
                            ]);
                            if (!$dismissedReport) {
                                DismissedReport::create([
                                    'user_id' => $user->id,
                                    'start_date' => date('Y-m-d'),
                                    'end_date' => $newDateThreeMonth,
                                    'dismissed_termination_period' =>  $diffThreeMonths,
                                ]);
                            }
                        } elseif (isset($data['contract_termination_period']) && $data['contract_termination_period'] == TerminateTime::SIX_MONTH) {
                            $contract->update([
                                'end_contract_date' => $newDateSixMonth,
                                "contract_termination_date" => date('Y-m-d'),
                                "contract_termination_period" => $diffSixMonths,
                                "contract_termination_reason" => $data['contract_termination_reason'],
                            ]);
                            if (!$dismissedReport) {
                                DismissedReport::create([
                                    'user_id' => $user->id,
                                    'start_date' => date('Y-m-d'),
                                    'end_date' => $newDateSixMonth,
                                    'dismissed_termination_period' =>   $diffSixMonths,
                                ]);
                            }
                        } elseif (isset($data['contract_termination_period']) && $data['contract_termination_period'] == TerminateTime::ONE_YEAR) {
                            $contract->update([
                                'end_contract_date' => $newDateYear,
                                "contract_termination_date" => date('Y-m-d'),
                                "contract_termination_period" => $diffYears,
                                "contract_termination_reason" => $data['contract_termination_reason'],
                            ]);
                            if (!$dismissedReport) {
                                DismissedReport::create([
                                    'user_id' => $user->id,
                                    'start_date' => date('Y-m-d'),
                                    'end_date' => $newDateYear,
                                    'dismissed_termination_period' =>   $diffYears,
                                ]);
                            }
                        } elseif (isset($data['contract_termination_period']) && $data['contract_termination_period'] == TerminateTime::OPEN_TERM) {
                            $contract->update([
                                'end_contract_date' => date('Y-m-d'),
                                "contract_termination_date" => date('Y-m-d'),
                                "contract_termination_period" => 'Undefined',
                                "contract_termination_reason" => $data['contract_termination_reason'],
                            ]);
                        }
                        $user->update([
                            'status' => EmployeeStatus::TEMPORARY_DISMISSED,
                        ]);
                    } elseif ($data['terminate_type'] == TerminateType::PERMANENT) {
                        $user->update([
                            'status' => EmployeeStatus::PERMANENT_DISMISSED,
                        ]);
                        $contract->update([
                            'end_contract_date' => date('Y-m-d'),
                            "contract_termination_date" => date('Y-m-d'),
                            "contract_termination_period" => 'Dismissed',
                            "contract_termination_reason" => 'Dismissed',
                        ]);
                    }
                } else {
                    return ['success' => false, 'message' => "You Cannot Terminate This Employee Because This Employee Have Deposit Unpaided Or UN PAID REJECTED."];
                }
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
            DB::commit();
            return ['success' => true, 'data' => $contract->load('user')];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function attendance_overview($filter)
    {
        if (auth()->user()->type == UserTypes::HR || auth()->user()->type == UserTypes::ADMIN) {
            $records = Percentage::query()->where('company_id', auth()->user()->company_id);
            if ($filter instanceof AttendanceOverviewFilter) {
                $records->when(isset($filter->year), function ($records) use ($filter) {
                    $records->where('year', 'LIKE', $filter->getYear());
                });
                return ['success' => true, 'data' => $records->get()];
            }
            return ['success' => true, 'data' => $records->get()];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function check_in_attendance($data)
    {
        DB::beginTransaction();
        try {
            $userId = auth()->user()->id;
            $user = auth()->user();
            $date = date('Y-m-d');
            $current_time = Carbon::now();
            $existingAttendance = Attendance::where('user_id', $userId)
                ->where('date', $date)
                ->where('login_time', '<>', null)
                ->where('logout_time', null)
                ->where('status', 1)
                ->latest()
                ->first();
            $existingOverTimeAttendance = OverTimeAttendance::where('user_id', $userId)
                ->where('date', $date)
                ->where('login_time', '<>', null)
                ->where('logout_time', null)
                ->where('status', 1)
                ->latest()
                ->first();
            $company = Company::where('id', $user->company_id)->first();
            if (isset($data['longitude']) && isset($data['latitude']) && isset($data['mac_address'])) {
                return ['success' => false, 'message' => 'Please choose either Location or Mac Address, not both.'];
            }
            if (isset($data['longitude']) && isset($data['latitude']) && !isset($data['mac_address'])) {

                $company_location = Location::where('company_id', $company->id)->first();
                $longitude_company = $company_location->longitude;
                $latitude_company = $company_location->latitude;
                $longitude_employee = $data['longitude'];
                $latitude_employee = $data['latitude'];
                $threshold = intval($company_location->radius);
                $earthRadius = 6371000;
                $lat1 = deg2rad($longitude_company);
                $lon1 = deg2rad($latitude_company);
                $lat2 = deg2rad($longitude_employee);
                $lon2 = deg2rad($latitude_employee);
                $deltaLat = $lat2 - $lat1;
                $deltaLon = $lon2 - $lon1;
                $a = sin($deltaLat / 2) * sin($deltaLat / 2) + cos($lat1) * cos($lat2) * sin($deltaLon / 2) * sin($deltaLon / 2);
                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $distance = $earthRadius * $c;
                if ($distance <= $threshold) {
                    $holidays = Holiday::where(function ($query) use ($date) {
                        $query->where('start_date', '<=', $date)
                            ->where('end_date', '>=', $date)
                            ->orWhere('start_date', '=', $date)
                            ->orWhere('end_date', '=', $date)
                            ->orWhere('day_name', date('l'));
                    })->get();
                    foreach ($holidays as  $holiday) {
                        if ($holiday && $holiday->type == HolidayTypes::WEEKLY) {
                            if ($existingOverTimeAttendance) {
                                return ['success' => false, 'message' => 'Attendance already exists'];
                            } else {
                                $overTimeAttendance  = new OverTimeAttendance();
                                $overTimeAttendance->user_id = $userId;
                                $overTimeAttendance->date = $date;
                                $overTimeAttendance->company_id = auth()->user()->company_id;
                                $overTimeAttendance->status = $data['check_in'];
                                $overTimeAttendance->login_time = $current_time->format('H:i:s');
                                $overTimeAttendance->save();
                                DB::commit();
                                return ['success' => true, 'data' =>  $overTimeAttendance->load('user')];
                            }
                        } elseif ($holiday && $holiday->type == HolidayTypes::ANNUL) {

                            $startDate = Carbon::parse($holiday->start_date);
                            $endDate = Carbon::parse($holiday->end_date);

                            if ($date >= $startDate && $date <= $endDate) {
                                if ($existingOverTimeAttendance) {
                                    return ['success' => false, 'message' => 'Attendance already exists'];
                                } else {
                                    $overTimeAttendance  = new OverTimeAttendance();
                                    $overTimeAttendance->user_id = $userId;
                                    $overTimeAttendance->date = $date;
                                    $overTimeAttendance->company_id = auth()->user()->company_id;
                                    $overTimeAttendance->status = $data['check_in'];
                                    $overTimeAttendance->login_time = $current_time->format('H:i:s');
                                    $overTimeAttendance->save();
                                    DB::commit();
                                    return ['success' => true, 'data' =>  $overTimeAttendance->load('user')];
                                }
                            }
                        }
                    }

                    $shifts = auth()->user()->shifts;
                    foreach ($shifts as $shift) {
                        $start_time = Carbon::parse(date('Y-m-d ') . $shift['start_time'], 'Asia/Riyadh')->timezone('Asia/Riyadh');
                        $end_time = Carbon::createFromFormat('H:i:s', $shift['end_time'])->timezone('Asia/Riyadh');

                        if ($current_time->between($start_time, $end_time)) {
                            if ($existingAttendance) {
                                return ['success' => false, 'message' => 'Attendance already exists'];
                            } else {
                                $attendance  = new Attendance();
                                $attendance->user_id = $userId;
                                $attendance->date = $date;
                                $attendance->company_id = auth()->user()->company_id;
                                $attendance->status = $data['check_in'];
                                $attendance->login_time = $current_time->format('H:i:s');
                                $attendance->save();
                                $newUser = User::where('id', auth()->user()->id)->first();
                                $newUser->update([
                                    'status' => EmployeeStatus::ACTIVE,
                                ]);
                                DB::commit();
                                return ['success' => true, 'data' =>  $attendance->load('user')];
                            }
                        } elseif (!$current_time->between($start_time, $end_time)) {
                            if ($existingOverTimeAttendance) {
                                return ['success' => false, 'message' => 'Attendance already exists'];
                            } else {
                                $overTimeAttendance  = new OverTimeAttendance();
                                $overTimeAttendance->user_id = $userId;
                                $overTimeAttendance->date = $date;
                                $overTimeAttendance->company_id = auth()->user()->company_id;
                                $overTimeAttendance->status = $data['check_in'];
                                $overTimeAttendance->login_time = $current_time->format('H:i:s');
                                $overTimeAttendance->save();
                                $newUser = User::where('id', auth()->user()->id)->first();
                                $newUser->update([
                                    'status' => EmployeeStatus::ACTIVE,
                                ]);
                                DB::commit();
                                return ['success' => true, 'data' =>  $overTimeAttendance->load('user')];
                            }
                        }
                    }
                } else {
                    return ['success' => false, 'message' => 'You Cannot Checkin Company,Because You Are outside the domain of the company'];
                }
            } elseif (isset($data['mac_address']) && !isset($data['longitude']) && !isset($data['latitude'])) {
                $company_addresses = $company->addresess->pluck('mac_address')->toArray();
                $current_time = Carbon::now();
                $macAddressMatched = false;
                foreach ($data['mac_address'] as $mac) {
                    if (in_array($mac, $company_addresses)) {
                        $macAddressMatched = true;
                        break;
                    }
                }
                if ($macAddressMatched) {

                    $holidays = Holiday::where(function ($query) use ($date) {
                        $query->where('start_date', '<=', $date)
                            ->where('end_date', '>=', $date)
                            ->orWhere('start_date', '=', $date)
                            ->orWhere('end_date', '=', $date)
                            ->orWhere('day_name', date('l'));
                    })->get();
                    foreach ($holidays as  $holiday) {

                        if ($holiday && $holiday->type == HolidayTypes::WEEKLY) {

                            if ($existingOverTimeAttendance) {
                                return ['success' => false, 'message' => 'Attendance already exists'];
                            } else {
                                $overTimeAttendance  = new OverTimeAttendance();
                                $overTimeAttendance->user_id = $userId;
                                $overTimeAttendance->date = $date;
                                $overTimeAttendance->company_id = auth()->user()->company_id;
                                $overTimeAttendance->status = $data['check_in'];
                                $overTimeAttendance->login_time = $current_time->format('H:i:s');
                                $overTimeAttendance->save();
                                $newUser = User::where('id', auth()->user()->id)->first();
                                $newUser->update([
                                    'status' => EmployeeStatus::ACTIVE,
                                ]);
                                DB::commit();
                                return ['success' => true, 'data' =>  $overTimeAttendance->load('user')];
                            }
                        } elseif ($holiday && $holiday->type == HolidayTypes::ANNUL) {

                            $startDate = Carbon::parse($holiday->start_date);
                            $endDate = Carbon::parse($holiday->end_date);
                            if ($date >= $startDate && $date <= $endDate) {
                                if ($existingOverTimeAttendance) {
                                    return ['success' => false, 'message' => 'Attendance already exists'];
                                } else {
                                    $overTimeAttendance  = new OverTimeAttendance();
                                    $overTimeAttendance->user_id = $userId;
                                    $overTimeAttendance->date = $date;
                                    $overTimeAttendance->company_id = auth()->user()->company_id;
                                    $overTimeAttendance->status = $data['check_in'];
                                    $overTimeAttendance->login_time = $current_time->format('H:i:s');
                                    $overTimeAttendance->save();
                                    $newUser = User::where('id', auth()->user()->id)->first();
                                    $newUser->update([
                                        'status' => EmployeeStatus::ACTIVE,
                                    ]);
                                    DB::commit();
                                    return ['success' => true, 'data' =>  $overTimeAttendance->load('user')];
                                }
                            }
                        }
                    }
                    $shifts = auth()->user()->shifts;

                    foreach ($shifts as $shift) {
                        $start_time = Carbon::parse(date('Y-m-d ') . $shift['start_time'], 'Asia/Riyadh')->timezone('Asia/Riyadh');
                        $end_time = Carbon::createFromFormat('H:i:s', $shift['end_time'])->timezone('Asia/Riyadh');

                        if ($current_time->between($start_time, $end_time)) {
                            if ($existingAttendance) {
                                return ['success' => false, 'message' => 'Attendance already exists'];
                            } else {
                                $attendance  = new Attendance();
                                $attendance->user_id = $userId;
                                $attendance->date = $date;
                                $attendance->company_id = auth()->user()->company_id;
                                $attendance->status = $data['check_in'];
                                $attendance->login_time = $current_time->format('H:i:s');
                                $attendance->save();
                                $newUser = User::where('id', auth()->user()->id)->first();
                                $newUser->update([
                                    'status' => EmployeeStatus::ACTIVE,
                                ]);
                                DB::commit();
                                return ['success' => true, 'data' =>  $attendance->load('user')];
                            }
                        } elseif (!$current_time->between($start_time, $end_time)) {
                            if ($existingOverTimeAttendance) {

                                DB::commit();
                                return ['success' => false, 'message' => 'Attendance already exists'];
                            } else {
                                $overTimeAttendance  = new OverTimeAttendance();
                                $overTimeAttendance->user_id = $userId;
                                $overTimeAttendance->date = $date;
                                $overTimeAttendance->company_id = auth()->user()->company_id;
                                $overTimeAttendance->status = $data['check_in'];
                                $overTimeAttendance->login_time = $current_time->format('H:i:s');
                                $overTimeAttendance->save();
                                $newUser = User::where('id', auth()->user()->id)->first();
                                $newUser->update([
                                    'status' => EmployeeStatus::ACTIVE,
                                ]);
                                DB::commit();
                                return ['success' => true, 'data' =>  $overTimeAttendance->load('user')];
                            }
                        }
                    }
                } else {
                    return ['success' => false, 'message' => 'You Cannot Checkin Company, Because You Are outside the domain of the company'];
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function check_out_attendance($data)
    {
        DB::beginTransaction();

        try {
            $userId = auth()->user()->id;
            $date = date('Y-m-d');
            $holidays = Holiday::where(function ($query) use ($date) {
                $query->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->orWhere('start_date', '=', $date)
                    ->orWhere('end_date', '=', $date)
                    ->orWhere('day_name', date('l'));
            })->get();
            $attendance = Attendance::where('user_id', $userId)
                ->where('date', $date)
                ->whereNotNull('login_time')
                ->where('logout_time', null)
                ->latest('login_time')
                ->first();
            $overTimeAttendance = OverTimeAttendance::where('user_id', $userId)
                ->where('date', $date)
                ->whereNotNull('login_time')
                ->where('logout_time', null)
                ->latest('login_time')
                ->first();
            $holidayDates = $holidays->pluck('start_date')->merge($holidays->pluck('end_date'))->merge($holidays->pluck('day_name')->map(function ($dayName) use ($date) {
                return Carbon::parse($date)->format('l') === $dayName ? $date : null;
            }))->filter()->unique();
            if ($holidayDates->contains($date) && $overTimeAttendance) {
                $overTimeAttendance->update([
                    'logout_time' => Carbon::now()->format('H:i:s'),
                ]);
                $newUser = User::where('id', auth()->user()->id)->first();
                $newUser->update([
                    'status' => EmployeeStatus::UN_ACTIVE,
                ]);
                DB::commit();

                return ['success' => true, 'data' => $overTimeAttendance->load('user')];
            } elseif (!$holidayDates->contains($date) && $overTimeAttendance) {
                $overTimeAttendance->update([
                    'logout_time' => Carbon::now()->format('H:i:s'),
                ]);
                $newUser = User::where('id', auth()->user()->id)->first();
                $newUser->update([
                    'status' => EmployeeStatus::UN_ACTIVE,
                ]);
                DB::commit();

                return ['success' => true, 'data' => $overTimeAttendance->load('user')];
            } elseif (!$holidayDates->contains($date) && $attendance) {
                $attendance->update([
                    'logout_time' => Carbon::now()->format('H:i:s'),
                ]);
                $newUser = User::where('id', auth()->user()->id)->first();
                $newUser->update([
                    'status' => EmployeeStatus::UN_ACTIVE,
                ]);
                DB::commit();

                return ['success' => true, 'data' => $attendance->load('user')];
            } else {
                return ['success' => false, 'message' => 'You Should Checkin Before Checkout Form System'];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function check_address($data)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($data['user_id']);
            $company = Company::where('id', $user->company_id)->first();
            $company_addresses = $company->addresess->pluck('mac_address')->toArray();

            $today_date = date("Y-m-d");
            $current_time = Carbon::now();
            $attendance = Attendance::where('date', $today_date)
                ->where('user_id', $data['user_id'])
                ->where('status', 1)
                ->whereNotNull('login_time')
                ->whereNull('logout_time')
                ->latest('login_time')
                ->latest()
                ->first();
            $overTimeAttendance = OverTimeAttendance::where('date', $today_date)
                ->where('user_id', $data['user_id'])
                ->where('status', 1)
                ->whereNotNull('login_time')
                ->whereNull('logout_time')
                ->latest('login_time')
                ->latest()
                ->first();
            if (isset($attendance) && !isset($overTimeAttendance)) {
                $checkedIn = false;
                foreach ($data['mac_address'] as $mac) {
                    if (in_array($mac, $company_addresses)) {
                        $checkedIn = true;
                        break;
                    }
                }

                if ($checkedIn) {
                    $attendance->update([
                        'custom_updated_at' => now()->format('Y-m-d H:i:s')
                    ]);
                    DB::commit();
                    return response()->json(['message' => "You Are Checked In"], 200);
                } else {
                    $attendance->update([
                        'logout_time' => $current_time->format('H:i:s')
                    ]);
                    $user = User::findOrFail($data['user_id']);
                    $user->update([
                        'status' => EmployeeStatus::UN_ACTIVE,
                    ]);

                    $title = "You Have a New Notification";
                    $body = "You are checked out of the company, you have exceeded the company domain";
                    $device_key = User::where('id', $user->id)->pluck('device_key')->first();
                    $notification = new Notification();
                    $notification->user_id = $user->id;
                    $notification->company_id = $user->company_id;
                    $notification->notifier_id = $user->id;
                    $notification->type = NotificationType::CHECK_OUT;
                    $notification->message = "You are checked out of the company, you have exceeded the company domain";
                    $notification->save();
                    $content = $notification->message;
                    $type = "notification";
                    if ($user->device_key != null) {
                        NotificationService::sendNotification($device_key, $body, $title, $content, $type);
                    }
                    DB::commit();
                    return response()->json(['message' => "You Are Checked Out"], 400);
                }
            } elseif (isset($overTimeAttendance) && !isset($attendance)) {
                $checkedIn = false;
                foreach ($data['mac_address'] as $mac) {
                    if (in_array($mac, $company_addresses)) {
                        $checkedIn = true;
                        break;
                    }
                }

                if ($checkedIn) {
                    $overTimeAttendance->update([
                        'custom_updated_at' => now()->format('Y-m-d H:i:s')
                    ]);
                    DB::commit();
                    return response()->json(['message' => "You Are Checked In"], 200);
                } else {
                    $overTimeAttendance->update([
                        'logout_time' => $current_time->format('H:i:s')
                    ]);
                    $user = User::findOrFail($data['user_id']);
                    $user->update([
                        'status' => EmployeeStatus::UN_ACTIVE,
                    ]);

                    $title = "You Have a New Notification";
                    $body = "You are checked out of the company, you have exceeded the company domain";
                    $device_key = User::where('id', $user->id)->pluck('device_key')->first();
                    $notification = new Notification();
                    $notification->user_id = $user->id;
                    $notification->company_id = $user->company_id;
                    $notification->notifier_id = $user->id;
                    $notification->type = NotificationType::CHECK_OUT;
                    $notification->message = "You are checked out of the company, you have exceeded the company domain";
                    $notification->save();
                    $content = $notification->message;
                    $type = "notification";
                    if ($user->device_key != null) {
                        NotificationService::sendNotification($device_key, $body, $title, $content, $type);
                    }
                    DB::commit();
                    return response()->json(['message' => "You Are Checked Out"], 400);
                }
            } else {
                return response()->json(['message' => "This Employee Doesn't Have Attendances Today."], 405);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function check_location($data)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($data['user_id']);
            $company = Company::where('id', $user->company_id)->first();
            $company_location = Location::where('company_id', $company->id)->first();
            $longitude_company = $company_location->longitude;
            $latitude_company = $company_location->latitude;
            $longitude_employee = $data['longitude'];
            $latitude_employee = $data['latitude'];
            $threshold = intval($company_location->radius);

            $today_date = date("Y-m-d");
            $current_time = Carbon::now();
            $attendance = Attendance::where('date', $today_date)
                ->where('user_id', $data['user_id'])
                ->where('status', 1)
                ->whereNotNull('login_time')
                ->whereNull('logout_time')
                ->latest('login_time')
                ->latest()
                ->first();
            $overTimeAttendance = OverTimeAttendance::where('date', $today_date)
                ->where('user_id', $data['user_id'])
                ->where('status', 1)
                ->whereNotNull('login_time')
                ->whereNull('logout_time')
                ->latest('login_time')
                ->latest()
                ->first();
            $earthRadius = 6371000;
            $lat1 = deg2rad($longitude_company);
            $lon1 = deg2rad($latitude_company);
            $lat2 = deg2rad($longitude_employee);
            $lon2 = deg2rad($latitude_employee);
            $deltaLat = $lat2 - $lat1;
            $deltaLon = $lon2 - $lon1;
            $a = sin($deltaLat / 2) * sin($deltaLat / 2) + cos($lat1) * cos($lat2) * sin($deltaLon / 2) * sin($deltaLon / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distance = $earthRadius * $c;

            if (isset($attendance) && !isset($overTimeAttendance)) {
                if ($distance <= $threshold) {

                    $attendance->update([
                        'custom_updated_at' => now()->format('Y-m-d H:i:s')
                    ]);
                    DB::commit();
                    return response()->json(['message' => "You Are Checked In"], 200);
                } elseif ($distance > $threshold) {

                    $attendance->update([
                        'logout_time' => $current_time->format('H:i:s')
                    ]);
                    $user = User::findOrFail($data['user_id']);
                    $user->update([
                        'status' => EmployeeStatus::UN_ACTIVE,
                    ]);

                    $title = "You Have New Notification";
                    $body = "You are checked out of the company, you have exceeded the company domain";
                    $device_key = User::where('id', $user->id)->pluck('device_key')->first();
                    $notification = new Notification();
                    $notification->user_id = $user->id;
                    $notification->company_id = $user->company_id;
                    $notification->notifier_id = $user->id;
                    $notification->type = NotificationType::CHECK_OUT;
                    $notification->message =  "You are checked out of the company, you have exceeded the company domain";
                    $notification->save();
                    $content = $notification->message;
                    $type = "notification";
                    if ($user->device_key != null) {
                        NotificationService::sendNotification($device_key, $body, $title, $content, $type);
                    }
                    DB::commit();
                    return response()->json(['message' => "You Are Checked Out"], 400);
                } else {
                    return response()->json(['message' => "This Employee Checkout Company Now."], 402);
                }
            } elseif (isset($overTimeAttendance) && !isset($attendance)) {
                if ($distance <= $threshold) {

                    $overTimeAttendance->update([
                        'custom_updated_at' => now()->format('Y-m-d H:i:s')
                    ]);
                    DB::commit();
                    return response()->json(['message' => "You Are Checked In"], 200);
                } elseif ($distance > $threshold) {

                    $attendance->update([
                        'logout_time' => $current_time->format('H:i:s')
                    ]);
                    $user = User::findOrFail($data['user_id']);
                    $user->update([
                        'status' => EmployeeStatus::UN_ACTIVE,
                    ]);

                    $title = "You Have New Notification";
                    $body = "You are checked out of the company, you have exceeded the company domain";
                    $device_key = User::where('id', $user->id)->pluck('device_key')->first();
                    $notification = new Notification();
                    $notification->user_id = $user->id;
                    $notification->company_id = $user->company_id;
                    $notification->notifier_id = $user->id;
                    $notification->type = NotificationType::CHECK_OUT;
                    $notification->message =  "You are checked out of the company, you have exceeded the company domain";
                    $notification->save();
                    $content = $notification->message;
                    $type = "notification";
                    if ($user->device_key != null) {
                        NotificationService::sendNotification($device_key, $body, $title, $content, $type);
                    }
                    DB::commit();
                    return response()->json(['message' => "You Are Checked Out"], 400);
                } else {
                    return response()->json(['message' => "This Employee Checkout Company Now."], 402);
                }
            } else {
                return response()->json(['message' => "This Employee Doesn't Have Attendances Today."], 405);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function update_working_hours($data)
    {
        DB::beginTransaction();
        try {
            $employeeAvailableTime =  EmployeeAvailableTime::where('user_id', $data['user_id'])->first();
            if ($employeeAvailableTime != null) {
                if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR && auth()->user()->company_id == $employeeAvailableTime->company_id) {
                    if (isset($data['hourly_annual']) && isset($data['daily_annual'])) {
                        $employeeAvailableTime->update([
                            'hourly_annual' => $data['hourly_annual'],
                            'daily_annual' => $data['daily_annual'],
                            'old_hourly_annual' => $data['hourly_annual'],
                            'old_daily_annual' => $data['daily_annual'],
                        ]);
                    } elseif (isset($data['hourly_annual'])) {

                        $employeeAvailableTime->update([
                            'hourly_annual' => $data['hourly_annual'],
                            'old_hourly_annual' => $data['hourly_annual'],
                        ]);
                    } elseif (isset($data['daily_annual'])) {
                        $employeeAvailableTime->update([
                            'daily_annual' => $data['daily_annual'],
                            'old_daily_annual' => $data['daily_annual'],
                        ]);
                    } else {
                        $employeeAvailableTime->update();
                    }
                } else {
                    return ['success' => false, 'message' => "Unauthorized"];
                }
            } else {

                return ['success' => false, 'message' => "Employee Doesn't Have Paid Vacation Hours"];
            }

            DB::commit();

            if ($employeeAvailableTime === null) {
                return ['success' => false, 'message' => "User was not created"];
            }

            return ['success' => true, 'data' => $employeeAvailableTime];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function reward_adversaries_salary($data)
    {
        $salary = Salary::where('user_id', $data['user_id'])->first();
        if (isset($data['rewards_type']) && $data['rewards_type'] == RewardsType::NUMBER) {
            $salary->update([
                'rewards' => $data['rewards'],
                'salary' =>  $salary->salary + $data['rewards'],
                'rewards_type' => $data['rewards_type'],
                'date' => date('Y-m-d'),
            ]);
        } elseif (isset($data['rewards_type']) && $data['rewards_type'] == RewardsType::RATE) {

            $oldSalary = $salary->salary;
            $rewardPercentage = $data['rewards'];
            $rewardAmount = ($oldSalary * $rewardPercentage) / 100;
            $totalSalary = $oldSalary + $rewardAmount;

            $salary->update([
                'rewards' => $data['rewards'],
                'salary' => $totalSalary,
                'rewards_type' => $data['rewards_type'],
                'date' => date('Y-m-d'),

            ]);
        }
        if (isset($data['adversaries_type']) && $data['adversaries_type'] == AdversariesType::NUMBER) {
            $salary->update([
                'adversaries' => $data['adversaries'],
                'salary' =>  $salary->salary - $data['adversaries'],
                'adversaries_type' => $data['adversaries_type'],
                'date' => date('Y-m-d'),
            ]);
        } elseif (isset($data['adversaries_type']) && $data['adversaries_type'] == AdversariesType::RATE) {
            $oldSalary = $salary->salary;
            $adversariesPercentage = $data['adversaries'];
            $adversariesAmount = ($oldSalary * $adversariesPercentage) / 100;
            $totalSalary = $oldSalary - $adversariesAmount;

            $salary->update([
                'adversaries' => $data['adversaries'],
                'salary' => $totalSalary,
                'adversaries_type' => $data['adversaries_type'],
                'date' => date('Y-m-d'),
            ]);
        }


        return ['success' => true, 'data' => $salary->load('user')];
    }

    public function profile()
    {
        return User::where('id', Auth::id())->with(['salaries', 'availableTime', 'requests', 'attendancesMonthly', 'deposits', 'dismissedReport', 'leaves'])->first();
    }

    public function model()
    {
        return User::class;
    }
}
