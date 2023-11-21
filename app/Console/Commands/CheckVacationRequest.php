<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Request;
use App\Statuses\PaymentType;
use App\Statuses\RequestStatus;
use App\Statuses\RequestType;
use App\Statuses\VacationRequestTypes;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckVacationRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vacation:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    // public function handle()
    // {
    //     $currentDate = Carbon::now()->toDateString();
    //     $currentTime = Carbon::now()->toTimeString();

    //     $requests = Request::where('start_date', $currentDate)
    //         ->where('type', RequestType::VACATION)
    //         ->where('vacation_type', VacationRequestTypes::HOURLY)
    //         ->where('status', RequestStatus::APPROVEED)
    //         ->where('payment_type', PaymentType::PAYMENT)
    //         ->where(function ($query) use ($currentTime) {
    //             $query->where('start_time', '<=', $currentTime);
    //         })
    //         ->get();

    //     foreach ($requests as $request) {
    //         $existingAttendance = Attendance::where('user_id', $request->user_id)
    //             ->where('date', $request->start_date)
    //             ->where('status', 1)
    //             ->where('login_time', $request->start_time)
    //             ->where('logout_time', $request->end_time)
    //             ->where('company_id', $request->company_id)
    //             ->first();

    //         if (!$existingAttendance) {
    //             Attendance::create([
    //                 'user_id' => $request->user_id,
    //                 'date' => $request->start_date,
    //                 'status' => 1,
    //                 'login_time' => $request->start_time,
    //                 'logout_time' => $request->end_time,
    //                 'company_id' => $request->company_id,
    //             ]);
    //         }
    //     }

    //     return Command::SUCCESS;
    // }
    public function handle()
    {
        $currentDate = Carbon::now()->toDateString();
        $currentTime = Carbon::now()->toTimeString();

        $requests = Request::where('start_date', $currentDate)
            ->where('type', RequestType::VACATION)
            ->where('vacation_type', VacationRequestTypes::HOURLY)
            ->where('status', RequestStatus::APPROVEED)
            ->where('payment_type', PaymentType::PAYMENT)
            ->where(function ($query) use ($currentTime) {
                $query->where('start_time', '<=', $currentTime);
            })
            ->get();

        $attendanceToInsert = [];

        foreach ($requests as $request) {
            $existingAttendance = Attendance::where('user_id', $request->user_id)
                ->where('date', $request->start_date)
                ->where('status', 1)
                ->where('login_time', $request->start_time)
                ->where('logout_time', $request->end_time)
                ->where('company_id', $request->company_id)
                ->first();

            if (!$existingAttendance) {
                $attendanceToInsert[] = [
                    'user_id' => $request->user_id,
                    'date' => $request->start_date,
                    'status' => 1,
                    'login_time' => $request->start_time,
                    'logout_time' => $request->end_time,
                    'company_id' => $request->company_id,
                ];
            }
        }

        if (!empty($attendanceToInsert)) {
            Attendance::insert($attendanceToInsert);
        }

        return Command::SUCCESS;
    }
}
