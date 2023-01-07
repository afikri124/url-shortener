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
        $schedule->job(new SyncAttendanceJob)->twiceDailyAt(3, 12, 30)->runInBackground()->withoutOverlapping(); //jam 03:30 dan 12:30
        // $schedule->job(new SyncAttOnlyJob)->twiceDaily(8, 9)->withoutOverlapping(); //jam 08:00 dan 09:00
        // $schedule->job(new SyncAttOnlyJob)->twiceDaily(10, 11)->withoutOverlapping();
        // $schedule->job(new SyncAttOnlyJob)->twiceDaily(13, 14)->withoutOverlapping();
        // $schedule->job(new SyncAttOnlyJob)->twiceDaily(15, 16)->withoutOverlapping();
        // $schedule->job(new SyncAttOnlyJob)->twiceDaily(17, 18)->withoutOverlapping();

        $schedule->job(new SyncAttOnlyJob)->hourly()->runInBackground()->withoutOverlapping();
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
