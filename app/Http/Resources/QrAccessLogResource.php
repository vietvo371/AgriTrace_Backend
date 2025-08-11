<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QrAccessLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'batch_id' => $this->batch_id,
            'access_time' => $this->access_time,
            'ip_address' => $this->ip_address,
            'device_info' => $this->device_info,
            // Include relationships when needed
            'batch' => new BatchResource($this->whenLoaded('batch')),
        ];
    }
}
