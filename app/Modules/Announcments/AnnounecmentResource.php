<?php

namespace App\Modules\Announcments;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnounecmentResource extends JsonResource
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
            "body" => $this->body,
            "date" => $this->time_to_post,
            "time" => $this->time,
            "department" => [
                "id" => $this->department->id,
                "name" => $this->department->name,
            ],
            "semester" => [
                "id" => $this->semester->id,
                "name" => $this->semester->name
            ],
            "course" => [
                "id" => $this->course->id,
                "name" => $this->course->name
            ],
            "user" => [
                "id" => $this->user->id,
                "name" => $this->user->name,
                "type" => $this->user->type,
                "avatar" => $this->user->avatar_url,
            ]

        ];
    }
}
