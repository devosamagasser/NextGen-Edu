<?php

namespace App\Modules\Halls;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HallResource extends JsonResource
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
            'code' => $this->building->code.$this->code.$this->floor,
            'name' => $this->name ?? null,
            'status' => $this->status,
            'floor' => $this->floor,
            'audience' => $this->audience,
            'building_name' => $this->building->name,
            'building_id' => $this->building->id,
            "latitude" => $this->building->latitude ,
            "longitude" => $this->building->longitude,
        ];
    }
}
