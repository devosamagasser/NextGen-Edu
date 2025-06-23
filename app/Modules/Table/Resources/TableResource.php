<?php

namespace App\Modules\Table\Resources;

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
            'course' => $this->course->name,
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
            'from' => $this->from,
            'to' => $this->to,
            'status' => $this->status,
            'postponed' => $this->postponed ? [
                'date' => $this->postponed->date,
                'day' => $this->postponed->day,
                'from' => $this->postponed->from,
                'to' => $this->postponed->to,
                'hall_id' => $this->postponed->hall_id,
                'attendance' => $this->postponed->attendance,
                'reason' => $this->postponed->reason,
            ] : null,
        ];
    }

}
