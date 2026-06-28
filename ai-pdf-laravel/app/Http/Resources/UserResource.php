<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => str_replace('-', '', $this->id),
            'name'              => $this->name,
            'email'             => $this->email,
            'organization_id'   => $this->organization_id,
            'organization'      => $this->when($this->organization, OrganizationResource::make($this->organization)),
            'is_active'         => $this->is_active,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}