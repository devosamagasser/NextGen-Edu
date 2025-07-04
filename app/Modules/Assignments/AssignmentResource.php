<?php

namespace App\Modules\Assignments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'teacher' => $this->teacher->user->name,
            'file' => $this->file_url,

            // ✅ For Student
            'answer_status' => $this->when($request->user()->type == 'Student' && $this->relationLoaded('answers'), function () use ($request) {
                $answer = $this->answers->where('student_id', $request->user()->students->id)->first();
                return $answer ? [
                    'status' => $answer->status,
                    'degree' => $answer->degree,
                    'file' => $answer->file_url,
                    'created_at' => $answer->created_at->format('Y-m-d H:i'),
                ] : null;
            }),

            // ✅ For Teacher
            'answers' => $this->when($request->user()->type == 'Teacher'&& $this->relationLoaded('answers'), function () {
                return $this->answers->map(function ($answer) {
                    return [
                        'id' => $answer->id,
                        'student' => $answer->student->user->name,
                        'code' => $answer->student->uni_code,
                        'status' => $answer->status,
                        'degree' => $answer->degree,
                        'file' => $answer->file_url,
                        'created_at' => $answer->created_at->format('Y-m-d H:i'),
                    ];
                })->values(); // ينظف الاندكسات
            }),

            'course' => [
                'id' => $this->course_detail_id,
                'name' => $this->course->name,
            ],
        ];
    }
}
