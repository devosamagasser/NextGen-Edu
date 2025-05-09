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
            'student' => $this->when($request->user()->type == 'Student', function () {
                $answer = $this->answers->first();
                return $answer ? [
                    'status' => $answer->status,
                    'degree' => $answer->degree,
                    'file' => $answer->file_url,
                    'created_at' => $answer->created_at->format('Y-m-d H:i'),
                ] : null;
            }),

            // ✅ For Teacher
            'students' => $this->when($request->user()->type == 'Teacher', function () {
                return $this->answers->map(function ($answer) {
                    return [
                        'student' => $answer->student->user->name,
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
