<?php

namespace App\Modules\Students;

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
        $department_id = auth()->user()->students->department_id;
        $semester_id = auth()->user()->students->semester_id;
        $department = $this->departments->find($department_id);
        $semester = $this->semesters->find($semester_id);
        return [
            'id' =>  $this->courseDetails->where('department_id', $department_id)
                    ->where('semester_id', $semester_id)->first()->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'department' => [
                'id' => $department->id,
                'name' => $department->name,
            ],
            'semester' => [
                'id' => $semester->id,
                'name' => $semester->name,
            ],
        ];
    }
}
