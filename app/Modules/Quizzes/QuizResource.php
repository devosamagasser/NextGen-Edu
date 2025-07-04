<?php

namespace App\Modules\Quizzes;

use Illuminate\Http\Request;
use App\Modules\Questions\Models\Question;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Questions\Reaources\QuestionResource;

class QuizResource extends JsonResource
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
            'date' => $this->date, 
            'start_time' => $this->start_time, 
            'duration' => $this->duration,
            'status' => $this->status,
            'teacher' => $this->teacher->user->name,
            'course' => [
                'id' => $this->course_detail_id,
                'name' => $this->course->name,
            ],
            'questions' => $this->whenLoaded('questions', function (){
                return QuestionResource::collection($this->questions);
            }),
        ];
    }
}
