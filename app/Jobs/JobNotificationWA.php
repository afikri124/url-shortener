<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class JobNotificationWA implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        //
        $this->data = $data;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        if(env('WA_TOKEN')){
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
            //   'target' => '087750431397,+6281233933313',
                'target' => $this->data['wa_to'],
                'message' => "[_Pesan ini dikirimkan otomatis oleh sistem_]\n".$this->data['wa_text'],
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: '.env('WA_TOKEN')
            ),
            ));

            $response = curl_exec($curl);
            $response = json_decode($response);
            // var_dump($response);
            if(isset($response->status)){
                if(!$response->status){
                    Log::warning($response);
                } 
            } else {
                Log::warning($response);
            }
            curl_close($curl);
        }
    }
}
