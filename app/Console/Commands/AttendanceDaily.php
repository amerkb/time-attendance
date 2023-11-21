<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\Holiday;
use App\Models\Request;
use App\Models\User;
use App\Statuses\EmployeeStatus;
use App\Statuses\RequestStatus;
use App\Statuses\RequestType;
use App\Statuses\UserTypes;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AttendanceDaily extends Command
{

    protected $signature = 'attendance:cron';

    protected $description = 'Command description';

    public function handle()
    {
        $today = Carbon::today();
        $companies = Company::all();
        $currentDayName = Carbon::now()->format('l');
        $currentDate = Carbon::now()->format('Y-m-d');

        foreach ($companies as $company) {
            $users = DB::table('users')
                ->whereIn('type', [UserTypes::EMPLOYEE, UserTypes::HR])
                ->leftJoin('attendances', function ($join) use ($today) {
                    $join->on('users.id', '=', 'attendances.user_id')
                        ->whereDate('attendances.date', $today);
                })
                ->whereNull('attendances.id')
                ->select('users.*')
                ->get();


            foreach ($users as $user) {
                $hasVacationRequests = Request::where('user_id', $user->id)
                    ->where('type', RequestType::VACATION)
                    ->where('status', RequestStatus::APPROVEED)
                    ->where(function ($query) use ($today) {
                        $query->whereDate('date', $today)
                            ->orWhere(function ($query) use ($today) {
                                $query->whereDate('start_date', '<=', $today)
                                    ->whereDate('end_date', '>=', $today);
                            });
                    })
                    ->exists();
                if ($hasVacationRequests) {
                    $newUser = User::where('id', $user->id)->first();
                    $newUser->update(['status' => EmployeeStatus::ON_VACATION]);
                    Attendance::create([
                        'user_id' => $user->id,
                        'date' => $today,
                        'status' => 0,
                        'company_id' => $company->id,
                    ]);
                } else {
                    $newUser = User::where('id', $user->id)->first();
                    $holidays = Holiday::where('company_id', $newUser->company_id)
                        ->where(function ($query) use ($currentDayName, $currentDate) {
                            $query->where('day_name', $currentDayName)
                                ->orWhere(function ($query) use ($currentDate) {
                                    $query->whereDate('start_date', '<=', $currentDate)
                                        ->whereDate('end_date', '>=', $currentDate);
                                });
                        })
                        ->get();

                    if ($holidays->isEmpty()) {
                        $newUser->update(['status' => EmployeeStatus::ABSENT]);
                        Attendance::create([
                            'user_id' => $user->id,
                            'date' => $today,
                            'status' => 0,
                            'company_id' => $company->id,
                        ]);
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
