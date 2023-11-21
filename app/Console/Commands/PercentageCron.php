<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\Percentage;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PercentageCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'percentage:cron';

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
        $current_month = date('m');
        $current_year = date('Y');

        $companies = Company::all();

        foreach ($companies as $company) {

            $total_attendances = Attendance::where('status', 0)->where('company_id', $company->id)
                ->whereMonth('date', $current_month)
                ->whereYear('date', $current_year)
                ->count();

            $total_attendance_days = Attendance::where('company_id', $company->id)->whereMonth('date', $current_month)
                ->whereYear('date', $current_year)
                ->count();

            $total_committed_users = Attendance::where('company_id', $company->id)->where('status', 1)
                ->whereMonth('date', $current_month)
                ->whereYear('date', $current_year)
                ->where(function ($query) {
                    $query->whereBetween('login_time', ['09:00:00', '09:15:00'])
                        ->orWhere('login_time', '09:00:00');
                })->count();

            if ($total_attendance_days > 0) {

                $percentage_absent_users = round(($total_attendances / $total_attendance_days) * 100);
                $percentage_committed_users = round(($total_committed_users / $total_attendance_days) * 100);
                $percentage_uncommitted_users = ($percentage_committed_users > 0) ? 100 - $percentage_committed_users : 0;
            } else {

                $percentage_absent_users = 0;
                $percentage_committed_users = 0;
                $percentage_uncommitted_users = 0;
            }

            Percentage::create([
                'rate_of_absents' => $percentage_absent_users,
                'rate_of_committed_employees' => $percentage_committed_users,
                'rate_of_un_committed_employees' => $percentage_uncommitted_users,
                'month' => $current_month,
                'year' => $current_year,
                'company_id' => $company->id

            ]);
        }

        return Command::SUCCESS;
    }
}
