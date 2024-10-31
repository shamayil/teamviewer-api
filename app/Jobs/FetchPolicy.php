<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class FetchPolicy implements ShouldQueue
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
        $api = config('config.team_viewer_api').'/devices';
        $token = config('config.token');
        $headers['Authorization'] = 'Bearer ' . $token;
        $client = new Client( [
            'headers' => $headers,
            'verify' => false
        ] );

        try {
            $response = $client->request('GET', $api)->getBody();
            $devices = json_decode($response,true);
            $data = $devices['devices'];
            $policy = $devices['policy_id'] ?? null;
            if ($data) {
                foreach ($data as $key => $item) {
                    if ( !$policy && $devices['online_state'] == "Online" ){
                        try{
                            $api = config('config.team_viewer_api').'/devices/'.$item['device_id'];
                            $token = config('config.token');
                            $headers['Authorization'] = 'Bearer ' . $token;
                            $client = new Client( [
                                'headers' => $headers,
                                'verify' => false
                            ] );
                            $client->request('PUT', $api,["form_params" => array (
                                'policy_id' => "712ee4e1-0fb2-461e-984f-bc020bf3a58e",
                                'assigned_to' => true,
                                'supported_features' => "remote_control, chat"
                            )])->getBody();
                        }catch (Throwable $e) {
                            return;
                        };
                    }
                };
            }
        }catch (Throwable $e) {
            return;
        }
    }
}
