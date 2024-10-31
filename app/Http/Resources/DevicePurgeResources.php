<?php

namespace App\Http\Resources;

use App\Constants\Messages;
use Illuminate\Http\Resources\Json\JsonResource;

class DevicePurgeResources extends JsonResource
{
    public function with($request)
    {
        return [
            'status' => 'OK',
            'message' => Messages::DEVICE_REMOVED_SUCCESSFULLY,
            'errors' => null,
        ];
    }

    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
