<?php

namespace App\Modules\Departments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'description' => $this->description,
            'teachers' => $this->teachers_count,
            'teachers' => $this->when(isset($this->teachers_count), $this->teachers_count),
            'courses' => $this->when(isset($this->courses_count), $this->courses_count),
            'students' => $this->when(isset($this->students_count), $this->students_count),
        ];
    }
}
