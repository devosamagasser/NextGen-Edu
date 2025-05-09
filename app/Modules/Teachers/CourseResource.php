<?php

namespace App\Modules\Teachers;

use Illuminate\Http\Request;
use App\Modules\Departments\DepartmentResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Courses\Resources\SemesterResource;

class CourseResource extends JsonResource
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
            'name' => $this->course->name,
            'code' => $this->course->code,
            'description' => $this->course->description,
            'students' => $this->students_count,
            'department' => new DepartmentResource($this->department),
            'semester' => new SemesterResource($this->semester),
        ];
    }
    
}
