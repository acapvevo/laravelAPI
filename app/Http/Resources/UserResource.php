<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'address' => new AddressResource($this->whenLoaded('address')),
            'phone_number' => new PhoneNumberResource($this->whenLoaded('phone_number')),
        ];
    }
}
