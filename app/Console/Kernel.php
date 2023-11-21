<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        Commands\AttendanceDaily::class,
        Commands\AlertCron::class,
        Commands\GenerateSalaries::class,
        Commands\PercentageCron::class,
        Commands\CheckLocation::class,
        Commands\ReviewContract::class,
        Commands\CheckShift::class,
        Commands\CheckShiftOut::class,
        Commands\CheckVacationRequest::class,

    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('attendance:cron')->dailyAt('23:59');
        $schedule->command('alert:cron')->dailyAt('23:59');
        $schedule->command('generate:salaries')->monthlyOn(now()->startOfMonth()->format('j'), '00:00');
        $schedule->command('percentage:cron')->monthlyOn(now()->endOfMonth()->format('j'), '23:59');
        $schedule->command('check:location')->everyFifteenMinutes();
        $schedule->command('review:contract')->dailyAt('23:59');
        $schedule->command('check:shift')->everyFifteenMinutes();
        $schedule->command('shift:out')->everyFifteenMinutes();
        $schedule->command('vacation:cron')->everyThirtyMinutes();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
