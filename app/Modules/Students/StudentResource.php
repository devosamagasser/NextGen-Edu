<?php

namespace App\Modules\Students;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'uni_code' => $this->uni_code,
            'email' => $this->user->email,
            'nationality' => $this->nationality,
            'personal_id' => $this->personal_id,
            'semester' => $this->semester->id,
            'department_id' => $this->department_id,
            'department_name' => $this->department->name,
            'group' => $this->group
        ];
    }
}
