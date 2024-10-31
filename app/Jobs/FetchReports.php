<?php

namespace App\Jobs;

use App\Models\RemoteLog;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Throwable;

class FetchReports implements ShouldQueue
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
        logger("Fetch Report Started");

        try {
            $api = config('config.team_viewer_api') . '/reports/connections';
            $token = config('config.token');
            $headers['Authorization'] = 'Bearer ' . $token;
            $client = new Client([
                'headers' => $headers,
                'verify' => false
            ]);
            $response = $client->request('GET', $api)->getBody();
            $reports = json_decode($response, true);
            $data = $reports['records'];
            if ($data) {
                foreach ($data as $key => $item) {
                    $collection = [
                        'deviceid' => $item['deviceid'],
                        'devicename' => $item['devicename'],
                        'groupid' => $item['groupid'],
                        'start_date' => Carbon::parse($item['start_date'])->format('Y/m/d H:m:s'),
                        'end_date' => Carbon::parse($item['end_date'])->format('Y/m/d H:m:s'),
                        'custno' => null,
                        'userid' => null,
                        'connecttime' => null,
                        'remotecontrol_id' => null,
                        'issuerno' => null,
                        'comment' => null
                    ];
                    RemoteLog::updateOrCreate(Arr::only($collection, [
                        'deviceid', 'start_date', 'end_date'
                    ]), array_merge($collection));
                }
            }
        } catch (Throwable $e) {
            logger('error connection fetch report');
            return;
        }
    }
}
