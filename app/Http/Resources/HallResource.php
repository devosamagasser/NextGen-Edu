<?php

namespace App\Http\Resources;

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
            'code' => $this->code,
            'name' => $this->building->code.$this->floor.$this->code,
            'floor' => $this->floor,
            'building' => [
                'id' =>  $this->building_id,
               'code' => $this->building->code ,
               'name' => $this->building->name ,
               'latitude' => $this->building->latitude ,
               'longitude' => $this->building->longitude ,
            ]
        ];
    }
}
