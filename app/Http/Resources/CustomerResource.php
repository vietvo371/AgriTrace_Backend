<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // For mobile profile endpoint
        if ($request->routeIs('user.profile')) {
            return [
                'full_name' => $this->full_name,
                'role' => $this->role,
                'farm_name' => $this->farm_name ?? '',
                'profile_image' => $this->profile_image ?? '',
            ];
        }

        // For other endpoints
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'profile_image' => $this->profile_image,
            'farm_name' => $this->farm_name,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Include relationships when needed
            'batches' => BatchResource::collection($this->whenLoaded('batches')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
