<?php

namespace App\Modules\CourseMaterials;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * 
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            "id" => $this->id,
            "title" => $this->title,
            "week" => $this->week,
            "file" => $this->material_url,
            'created_at' => $this->created_at->diffForHumans(),
            'type' => 'lecture'
            // 'department' => [
            //     "id" => $this->department_id,
            //     "name" => $this->department->name
            // ],
            // 'semester' => [
            //     "id" => $this->semester_id,
            //     "name" => $this->semester->name
            // ],
            // "course" => [
            //     "id" => $this->course_details_id,
            //     "name" => $this->course->name
            // ],
        ];
    }
}
