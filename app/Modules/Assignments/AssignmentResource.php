<?php

namespace App\Modules\Assignments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Questions\Reaources\QuestionResource;

class AssignmentResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'total_degree' => $this->total_degree,
            'date' => $this->deadline->format('Y-m-d'), 
            'time' => $this->deadline->format('H:i'), 
            'status' => $this->status,
            'course' => [
                'id' => $this->course_details_id,
                'name' => $this->course->name,
            ],
            'teacher' => $this->teacher->name,
            'file' => $this->file_url,
        ];
    }
}
