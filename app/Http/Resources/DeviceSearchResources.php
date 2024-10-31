<?php

namespace App\Http\Resources;

use App\Constants\Messages;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceSearchResources extends JsonResource
{
    public function with($request)
    {
        return [
            'status' => 'OK',
            'message' => Messages::DEVICE_FOUND_SUCCESSFULLY,
            'errors' => null,
        ];
    }

    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
