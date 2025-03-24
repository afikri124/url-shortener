<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncAttOnlyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        //jalankan fungsi dari controller ini
        app('App\Http\Controllers\WorkHoursController')->whr_sync(); //absen pada mesin

        //jalankan fungsi dari controller ini
        app('App\Http\Controllers\WorkHoursController')->siap_sync(); //sync data absen ke Siakadcloud (SIAP)
    }
}
