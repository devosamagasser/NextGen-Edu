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
        $department = new DepartmentResource($this->departments->find($this->pivot->department_id));
        $semester = new SemesterResource($this->semesters->find($this->pivot->semester_id));

        return [
            'id' => $this->pivot->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'department' => $department,
            'semester' => $semester,
        ];
    }
}
