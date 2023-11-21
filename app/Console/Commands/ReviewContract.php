<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\User;
use App\Statuses\EmployeeStatus;
use App\Statuses\UserTypes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReviewContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'review:contract';

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
        $current_date = \Carbon\Carbon::now();
        $temporaryDismissedEmployees = DB::table('users')
            ->whereIn('type', [UserTypes::EMPLOYEE, UserTypes::HR])
            ->where('status', EmployeeStatus::TEMPORARY_DISMISSED)
            ->get();

        $activeEmployees = User::whereIn('type', [UserTypes::EMPLOYEE, UserTypes::HR])
            ->whereNotIn('status', [EmployeeStatus::TEMPORARY_DISMISSED, EmployeeStatus::PERMANENT_DISMISSED])
            ->join('contracts', 'users.id', '=', 'contracts.user_id')
            ->where(function ($query) use ($current_date) {
                $query->where('end_contract_date', '=', $current_date)
                    ->orWhere('end_contract_date', '<', $current_date);
            })
            ->select('users.*')
            ->distinct()
            ->get();

        foreach ($temporaryDismissedEmployees as $employee) {
            DB::table('contracts')
                ->where('user_id', $employee->id)
                ->where(function ($query) use ($current_date) {
                    $query->where('end_contract_date', '=', $current_date)
                        ->orWhere('end_contract_date', '>', $current_date);
                })
                ->update(['end_contract_date' => $employee->end_job_contract]);

            DB::table('users')
                ->where('id', $employee->id)
                ->update(['status' => EmployeeStatus::UN_ACTIVE]);
        }

        foreach ($activeEmployees as $employee) {
            $employee->contract->update(['end_contract_date' => $employee->end_job_contract]);
            $employee->update(['status' => EmployeeStatus::PERMANENT_DISMISSED]);
        }

        return Command::SUCCESS;
    }
}
