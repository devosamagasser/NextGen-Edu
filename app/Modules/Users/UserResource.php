<?php

namespace App\Modules\Users;

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
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'type' => $this->type,
            'avatar' => asset($this->avatar)
        ];
        $this->whenLoaded('students',function() use(&$data){
            $data['semester'] = $this->students->semester->id;
            $data['semester_name'] = $this->students->semester->name;
            $data['department_id'] = $this->students->department_id;
            $data['department_name'] = $this->students->department->name;
            $data['group'] = $this->students->group;
        });
        return $data;
    }
}
