<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'name' => $this->name,
            'status' => $this->is_done ? 'finished' : 'open',
            'creator_id' => $this->id,
            'scheduled_at' => $this->scheduled_at,
            'due_at' => $this->due_at,
        ];
    }
}
