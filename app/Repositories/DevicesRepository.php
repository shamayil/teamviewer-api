<?php

namespace App\Repositories;

use App\Contracts\Devices as DeviceContract;
use App\Exceptions\UnprocessableEntityException;
use App\Models\CustomerRemote;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Arr;
use Throwable;


class DevicesRepository implements DeviceContract
{
    private $customerRemote;

    public function __construct(CustomerRemote $customerRemote)
    {
        $this->customerRemote = $customerRemote;
    }

    /**
     * Get page from identifier
     * @return mixed
     */
    public function handle(array $values)
    {
        $remoteID = "r" . Arr::get($values, 'remote_id');
        $name = Arr::get($values, 'device_name');
        $pass = Arr::get($values, 'device_password');
        $groupId = "g161825826";

        try {
            $api = config('config.team_viewer_api') . '/devices';
            $token = config('config.token');
            $headers['Authorization'] = 'Bearer ' . $token;
            $client = new Client([
                'headers' => $headers,
                'verify' => false
            ]);
            $client->request('POST', $api, ["form_params" => array(
                'remotecontrol_id' => $remoteID,
                'groupid' => $groupId,
                'alias' => $name,
                'password' => $pass
            )])->getBody();
            return;
        } catch (Throwable $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents(), true);
            throw new UnprocessableEntityException(
                $error['error_description'],
                $error['error']
            );
        }
    }

    public function edit(array $values)
    {
        $data = [];
        $deviceID = Arr::get($values, 'device_id');
        $name = Arr::get($values, 'device_name') ?? null;
        $pass = Arr::get($values, 'device_password') ?? null;
        $policyId = Arr::get($values, 'policy_id') ?? null;

        if ($name)
            $data['alias'] = $name;
        if ($policyId)
            $data['policy_id'] = $policyId;
        if ($pass)
            $data['password'] = $pass;

        try {
            $api = config('config.team_viewer_api') . '/devices/' . $deviceID;
            $token = config('config.token');
            $headers['Authorization'] = 'Bearer ' . $token;
            $client = new Client([
                'headers' => $headers,
                'verify' => false
            ]);
            $client->request('PUT', $api, ["form_params" => $data])->getBody();

        } catch (Throwable $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents(), true);
            throw new UnprocessableEntityException(
                $error['error_description'],
                $error['error']
            );
        }
    }

    public function remove(array $values)
    {
        $deviceID = Arr::get($values, 'device_id');
        try {
            $api = config('config.team_viewer_api') . '/devices/' . $deviceID;
            $token = config('config.token');
            $headers['Authorization'] = 'Bearer ' . $token;
            $client = new Client([
                'headers' => $headers,
                'verify' => false
            ]);
            $client->request('DELETE', $api)->getBody();

        } catch (Throwable $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents(), true);
            throw new UnprocessableEntityException(
                $error['error_description'],
                $error['error']
            );
        }
    }

    public function device(array $values)
    {
        $deviceID = Arr::get($values, 'device_id') ?? null;
        $customerID = Arr::get($values, 'customer_id') ?? null;

        if ($deviceID) {
            try {
                $api = config('config.team_viewer_api') . '/devices/' . $deviceID;
                $token = config('config.token');
                $headers['Authorization'] = 'Bearer ' . $token;
                $client = new Client([
                    'headers' => $headers,
                    'verify' => false
                ]);
                $response = $client->request('GET', $api)->getBody();
                $response = json_decode($response, true);
                return $response['devices'][0];
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    $error = json_decode($e->getResponse()->getBody()->getContents(), true);
                    throw new UnprocessableEntityException(
                        $error['error_description'] ?? 'An error occurred',
                        $error['error'] ?? 0
                    );
                } else {
                    throw new UnprocessableEntityException(
                        'No response from server',
                        0
                    );
                }
            } catch (Throwable $e) {
                throw new UnprocessableEntityException(
                    'An unexpected error occurred',
                    0
                );
            }
        } else {
            $remote = $this->customerRemote->where('custno', $customerID)->get()->toArray();
            $collection = [];
            foreach ($remote as $item) {
                $device = [
                    'device_id' => $item['device_id'],
                ];
                $result = $this->device($device);
                array_push($collection, $result);
            }
            return $collection;
        }
    }
}
