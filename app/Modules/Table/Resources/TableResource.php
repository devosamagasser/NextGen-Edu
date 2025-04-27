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
            'course' => $this->details->course->name,
            'teacher' => $this->details->teacher->user->name,
            'department' => $this->details->department->name,
            'semester' => $this->details->semester->id,
            'hall' => [
                'hall_id' => $hall->id,
                'hall_name' => $hall->name,
                'hall_code' => $hall_code,
                'building' => $this->hall->building->name,
                'latitude' => $this->hall->building->latitude,
                'longitude' => $this->hall->building->longitude,
            ],
            'attendance' => $this->attendance,
            'day' => $this->day,
            'date' => $this->date,
            'from' => $this->from,
            'to' => $this->to,
            'week' => $this->week ?? null,
            'status' => $this->status,
        ];
    }
}
