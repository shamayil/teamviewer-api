<?php

namespace App\Http\Controllers;

use App\Contracts\Devices as DeviceContract;
use App\Http\Requests\DeviceAddRequest;
use App\Http\Requests\DeviceEditRequest;
use App\Http\Requests\DevicePurgeRequest;
use App\Http\Requests\DeviceSearchRequest;
use App\Http\Resources\DeviceAddResources;
use App\Http\Resources\DeviceEditResources;
use App\Http\Resources\DevicePurgeResources;
use App\Http\Resources\DeviceSearchResources;

class DeviceController extends Controller
{
    private $devices;

    public function __construct(DeviceContract $devices)
    {
        $this->devices = $devices;
    }

    public function handle(DeviceAddRequest $request)
    {
        return new DeviceAddResources(
            $this->devices->handle(
                $request->validated()
            )
        );
    }

    public function edit(DeviceEditRequest $request)
    {
        return new DeviceEditResources(
            $this->devices->edit(
                $request->validated()
            )
        );
    }

    public function remove(DevicePurgeRequest $request)
    {
        return new DevicePurgeResources(
            $this->devices->remove(
                $request->validated()
            )
        );
    }

    public function device(DeviceSearchRequest $request)
    {
        return new DeviceSearchResources(
            $this->devices->device(
                $request->validated()
            )
        );
    }

}
