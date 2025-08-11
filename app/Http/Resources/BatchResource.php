<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
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
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'batch_code' => $this->batch_code,
            'weight' => $this->weight,
            'variety' => $this->variety,
            'planting_date' => $this->planting_date,
            'harvest_date' => $this->harvest_date,
            'cultivation_method' => $this->cultivation_method,
            'location' => $this->location,
            'gps_coordinates' => $this->gps_coordinates,
            'qr_code' => $this->qr_code,
            'qr_expiry' => $this->qr_expiry,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Include relationships when needed
            'user' => new UserResource($this->whenLoaded('user')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'images' => BatchImageResource::collection($this->whenLoaded('images')),
            'access_logs' => QrAccessLogResource::collection($this->whenLoaded('accessLogs')),
        ];
    }
}
