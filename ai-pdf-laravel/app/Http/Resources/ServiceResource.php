<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'service_id' => $this->_id,
            'file_id' => $this->file_id,
            'file_name' => $this->whenLoaded('file', fn() => $this->file->file_name),
            'organization_id' => $this->organization_id,
            'type' => $this->type,
            'status' => $this->status,
            'target_language' => $this->target_language,
            'output' => $this->output,
            'created_at' => $this->created_at ? $this->created_at->toISOString() : null,
            'completed_at' => $this->completed_at ? $this->completed_at->toISOString() : null,
            'is_completed' => $this->isCompleted(),
            'is_pending' => $this->isPending(),
            'is_processing' => $this->isProcessing(),
            'is_failed' => $this->isFailed(),
            'notification_sent' => (bool) $this->notification_sent,
            'notification_seen' => (bool) $this->notification_seen,
            'notified_at' => $this->notified_at ? $this->notified_at->toISOString() : null,
        ];
    }
}
