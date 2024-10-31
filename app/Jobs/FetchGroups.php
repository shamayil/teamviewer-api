<?php

namespace App\Jobs;

use App\Models\RemoteDuplicated;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Throwable;

class FetchGroups implements ShouldQueue
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
        $api = config('config.team_viewer_api') . '/devices';
        $token = config('config.token');
        $headers['Authorization'] = 'Bearer ' . $token;
        $client = new Client([
            'headers' => $headers,
            'verify' => false
        ]);

        try {
            $response = $client->request('GET', $api)->getBody();
            $devices = json_decode($response, true);
            $data = $devices['devices'];
            if ($data) {
                foreach ($data as $key => $item) {
                    if ($item['groupid'] != "g161825826") {
                        try {
                            $api = config('config.team_viewer_api') . '/devices/' . $item['device_id'];
                            $token = config('config.token');
                            $headers['Authorization'] = 'Bearer ' . $token;
                            $client = new Client([
                                'headers' => $headers,
                                'verify' => false
                            ]);
                            $client->request('PUT', $api, ["form_params" => array(
                                'groupid' => "g161825826",
                            )])->getBody();
                        } catch (Throwable $e) {
                            $this->fetch($item);
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            return;
        }
    }


    public function fetch($item)
    {
        $lastSeen = $item['last_seen'] ?? null;
        $remotecontrol_id = str_replace('r', '', $item['remotecontrol_id']);

        if ($lastSeen)
            $lastSeen = Carbon::parse($lastSeen)->format('Y/m/d H:m:s');

        $collection = [
            'custrno' => null,
            'remotecontrol_id' => $remotecontrol_id,
            'device_id' => $item['device_id'],
            'alias' => $item['alias'],
            'groupid' => $item['groupid'],
            'online_state' => $item['online_state'],
            'policy_id' => $item['policy_id'] ?? null,
            'assigned_to' => $item['assigned_to'] ?? null,
            'last_seen' => $lastSeen,
            'posremoteid' => null,
            'description' => $item['description'] ?? null,
            'supported_features' => $item['supported_features'] ?? null
        ];

        RemoteDuplicated::updateOrCreate(Arr::only($collection, [
            'remotecontrol_id', 'alias', 'description'
        ]), array_merge($collection));

        $this->remove($item);
    }


    public function remove($item)
    {
        try {
            $api = config('config.team_viewer_api') . '/devices/' . $item['device_id'];
            $token = config('config.token');
            $headers['Authorization'] = 'Bearer ' . $token;
            $client = new Client([
                'headers' => $headers,
                'verify' => false
            ]);
            $client->request('DELETE', $api)->getBody();
        } catch (Throwable $e) {
            return;
        }
    }

}
