<?php

namespace App\Modules\Teachers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
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
            'name' => $this->user->name,
            'email' => $this->user->email,
            'uni_code' => $this->uni_code,
            'description' => $this->description,
            'department' => $this->department->name,
        ];
    }
}
