<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SyncAttendanceJob;
use App\Jobs\SyncAttOnlyJob;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('queue:retry all')
        ->twiceDaily(10, 15)
        ->withoutOverlapping();
        $schedule->job(new SyncAttendanceJob)->twiceDaily(3, 12)->withoutOverlapping(); //jam 03:00 dan 12:00
        $schedule->job(new SyncAttOnlyJob)->twiceDaily(9, 17)->withoutOverlapping(); //jam 09:00 dan 17:00
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
