<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command('exchange:update')->dailyAt('09:00');

        // Har kuni 23:59 da kassani yopish
//        $schedule->command('cashreport:auto-close')->dailyAt('23:59');

        $schedule->command('cashreport:auto-close')->dailyAt('23:59')->appendOutputTo(storage_path('logs/cashreport.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
