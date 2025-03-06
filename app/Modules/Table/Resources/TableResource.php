<?php

namespace App\Modules\Table\Resources;

use App\Modules\Courses\Resources\DepartmentResource;
use App\Modules\Courses\Resources\SemesterResource;
use App\Modules\Courses\Resources\TeacherResource;
use App\Modules\Halls\HallResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $hall = new HallResource($this->hall);
        $hall_code = $this->hall->building->code.$this->hall->floor.$this->hall->code;

        return [
            'id' => $this->id,
            'type' => $this->type,
            'course_id' => $this->details->course->id,
            'course_name' => $this->details->course->name,
            'teacher_id' => $this->details->teacher->id,
            'teacher_name' => $this->details->teacher->user->name,
            'department_id' => $this->details->department->id,
            'department_name' => $this->details->department->name,
            'semester' => $this->details->semester->id,
            'hall_id' => $hall->id,
            'hall_name' => $hall->name,
            'hall_code' => $hall_code,
            'latitude' => $this->hall->building->latitude,
            'longitude' => $this->hall->building->longitude,
            'building_id' => $this->hall->building_id,
            'building_name' => $this->hall->building->name,
            'attendance' => $this->attendance,
            'day' => $this->day,
            'date' => $this->date,
            'from' => $this->from,
            'to' => $this->to,
            'week' => $this->week,
            'status' => $this->status,
        ];
    }
}
