<?php

namespace App\Http\Resources;

use App\Constants\Messages;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceAddResources extends JsonResource
{
    public function with($request)
    {
        return [
            'status' => 'OK',
            'message' => Messages::DEVICE_ADDED_SUCCESSFULLY,
            'errors' => null,
        ];
    }

    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
