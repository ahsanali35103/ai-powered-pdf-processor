<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'file_id' => $this->_id,
            'file_name' => $this->file_name,
            'file_size' => $this->formatted_size,
            'file_type' => $this->mime_type,
            'upload_status' => $this->upload_status,
            'uploaded_at' => $this->created_at ? $this->created_at->toISOString() : null,
        ];
    }
}
