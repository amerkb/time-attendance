<?php

namespace App\Console\Commands;

use App\Events\AlerAdminEndContractEvent;
use App\Events\AlerAdminEndPassportEvent;
use App\Models\User;
use App\Statuses\UserTypes;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AlertCron extends Command
{
    protected $signature = 'alert:cron';

    protected $description = 'Command description';

    public function handle()
    {
        $approachingExpirationUsers = User::whereIn('type', [UserTypes::EMPLOYEE, UserTypes::HR])
            ->whereNotNull('end_job_contract')
            ->where('end_job_contract', '<=', Carbon::now()->addMonth())
            ->get(['id', 'name', 'start_job_contract', 'end_job_contract']);


        $passportExpirationEmployees = User::whereIn('type', [UserTypes::EMPLOYEE, UserTypes::HR])
            ->whereNotNull('end_passport')
            ->where('end_passport', '<=', Carbon::now()->addMonth())
            ->get(['id', 'name', 'end_passport']);



        if (count($approachingExpirationUsers) > 0) {
            foreach ($approachingExpirationUsers as $user) {
                event(new AlerAdminEndContractEvent($user));
            }
        }


        if (count($passportExpirationEmployees) > 0) {
            foreach ($passportExpirationEmployees as $user) {
                event(new AlerAdminEndPassportEvent($user));
            }
        }

        return Command::SUCCESS;
    }
}
