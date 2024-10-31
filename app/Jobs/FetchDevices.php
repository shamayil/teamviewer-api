<?php

namespace App\Jobs;

use App\Models\CustomerRemote;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class FetchDevices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        logger("Fetch Devices Started");
        $api = config('config.team_viewer_api') . '/devices';
        $token = config('config.token');
        $headers['Authorization'] = 'Bearer ' . $token;
        $client = new Client([
            'headers' => $headers,
            'verify' => false
        ]);

        try {
            logger("Fetch Devices trying");
            $response = $client->request('GET', $api)->getBody();
            $devices = json_decode($response, true);
            $data = $devices['devices'];
            if ($data) {
                foreach ($data as $key => $item) {
                    $lastSeen = $item['last_seen'] ?? null;
                    $remotecontrol_id = str_replace('r', '', $item['remotecontrol_id']);
                    if ($lastSeen)
                        $lastSeen = Carbon::parse($lastSeen)->format('Y/m/d H:m:s');
                    $policy = $item['policy_id'] ?? null;
                    $assigned = $item['assigned_to'] ?? null;
                    $description = $item['description'] ?? null;
                    $features = $item['supported_features'] ?? null;

                    $collection = [
                        'custrno' => null,
                        'remotecontrol_id' => $remotecontrol_id,
                        'device_id' => $item['device_id'],
                        'alias' => $item['alias'],
                        'groupid' => $item['groupid'],
                        'online_state' => $item['online_state'],
                        'policy_id' => $policy,
                        'assigned_to' => $assigned,
                        'last_seen' => $lastSeen,
                        'posremoteid' => null,
                        'description' => $description,
                        'supported_features' => $features,
                        'update_time' => Carbon::now()->toDateTimeString()
                    ];

                    try {
                        $checkRemote = CustomerRemote::where('remotecontrol_id', $remotecontrol_id)->first();
                        if (!$checkRemote) {
                            CustomerRemote::create($collection);
                        } else {
                            CustomerRemote::where('remotecontrol_id', $remotecontrol_id)->update(
                                array('alias' => $item['alias'],
                                    'device_id' => $item['device_id'],
                                    'groupid' => $item['groupid'],
                                    'online_state' => $item['online_state'],
                                    'policy_id' => $policy,
                                    'assigned_to' => $assigned,
                                    'last_seen' => $lastSeen,
                                    'description' => $description,
                                    'supported_features' => $features,
                                    'update_time' => Carbon::now()->toDateTimeString()
                                )
                            );
                        }
                    } catch (Throwable $e) {
                        logger('error update on remote id ' . $remotecontrol_id);
                    }
                }
            }
            logger("Fetching Done");
        } catch (Throwable $e) {
            return;
        }
    }
}
