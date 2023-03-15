<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SyncAttendanceJob;
use App\Jobs\SyncAttOnlyJob;
use App\Jobs\BroadCastNotificationDoc;
use App\Jobs\WeeklyAttendanceReport;

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
        $schedule->job(new SyncAttOnlyJob)->hourly()->runInBackground()->withoutOverlapping();
        $schedule->job(new BroadCastNotificationDoc)->days([1,2,3,4,5,6])->at('15:30')->runInBackground()->withoutOverlapping();
        $schedule->job(new WeeklyAttendanceReport)->weeklyOn(5, '17:00')->runInBackground()->withoutOverlapping();
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
