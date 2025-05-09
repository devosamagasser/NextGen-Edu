<?php

namespace App\Modules\Assignments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentAnswersResource extends JsonResource
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
            'teacher' => $this->teacher->user->name,
            'file' => $this->file_url,
            'course' => [
                'id' => $this->course_detail_id,
                'name' => $this->course->name,
            ],
        ];
    }
    
}
