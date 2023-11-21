<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Shift;
use App\Models\User;
use App\Statuses\EmployeeStatus;
use App\Statuses\UserTypes;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckShift extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:shift';

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

    public function handle()
    {
        $currentDateTime = Carbon::now();
        $currentTime = Carbon::now()->format('H:i:s');

        $users = User::whereIn('type', [UserTypes::HR, UserTypes::EMPLOYEE])->whereHas('attendances', function ($query) use ($currentDateTime) {
            $query->where('login_time', '!=', null)
                ->where('logout_time', null)
                ->where('status', 1)
                ->whereDate('date', $currentDateTime->toDateString());
        })->get();

        foreach ($users as $user) {
            if ($user->shifts) {
                $isWithinShift = false;
                foreach ($user->shifts as $shift) {
                    if ($currentTime >= $shift->start_time && $currentTime <= $shift->end_time) {
                        $isWithinShift = true;
                        break;
                    }
                }

                if (!$isWithinShift) {
                    $latestAttendance = Attendance::where('user_id', $user->id)
                        ->where('login_time', '!=', null)
                        ->where('logout_time', null)
                        ->where('status', 1)
                        ->whereDate('date', $currentDateTime->toDateString())
                        ->latest()
                        ->first();

                    if ($latestAttendance) {
                        $latestAttendance->update(['logout_time' => $currentTime]);
                        $user->update(['status' => EmployeeStatus::UN_ACTIVE]);
                    }
                }
            }
        }
    }
}
