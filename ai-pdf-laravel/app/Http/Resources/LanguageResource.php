<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name
        ];
    }
}
