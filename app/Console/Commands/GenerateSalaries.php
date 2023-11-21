<?php

namespace App\Console\Commands;

use App\Models\Salary;
use App\Models\User;
use App\Statuses\UserTypes;
use Illuminate\Console\Command;

class GenerateSalaries extends Command
{
    protected $signature = 'generate:salaries';
    protected $description = 'Generate new salaries for all employees on the first day of every month';


    public function handle()
    {
        $employees = User::whereIn('type', [UserTypes::HR, UserTypes::EMPLOYEE])->get();

        foreach ($employees as $employee) {
            if ($employee->next_month_salary != null) {
                $salary = new Salary();
                $salary->user_id = $employee->id;
                $salary->salary = $employee->next_month_salary;
                $salary->rewards = 0;
                $salary->adversaries = 0;
                $salary->housing_allowance = 0;
                $salary->transportation_allowance = 0;
                $salary->basic_salary = $employee->next_month_salary;
                $salary->date = now()->startOfMonth();
                $salary->save();
                $employee->basic_salary =  $employee->next_month_salary;
                $employee->next_month_salary =  0;
                $employee->save();
            } else {
                $salary = new Salary();
                $salary->user_id = $employee->id;
                $salary->salary = $employee->next_month_salary;
                $salary->rewards = 0;
                $salary->adversaries = 0;
                $salary->housing_allowance = 0;
                $salary->transportation_allowance = 0;
                $salary->basic_salary = $employee->basic_salary;
                $salary->date = now()->startOfMonth();
                $salary->save();
            }
        }

        return Command::SUCCESS;
    }
}
