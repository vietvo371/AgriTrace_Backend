<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchImageResource extends JsonResource
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
            'image_url' => $this->image_url,
            'image_type' => $this->image_type,
            // Include relationships when needed
            'batch' => new BatchResource($this->whenLoaded('batch')),
        ];
    }
}
