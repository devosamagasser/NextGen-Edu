<?php

namespace App\Modules\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'type' => $this->type,
            'avatar' => $this->avatar_url
        ];

        $this->whenLoaded('students',function() use(&$data){
            $data['nationality'] = $this->students->nationality;
            $data['uni_code'] = $this->students->uni_code;
            $data['personal_id'] = $this->students->personal_id;
            $data['group'] = $this->students->group;
            $data['semester'] =[
                'id' => $this->students->semester->id,
                'name' => $this->students->semester->name
            ];
            $data['department'] = [
                'id' => $this->students->department->id,
                'name' => $this->students->department->name
            ];
            $data['group'] = $this->students->group;
        });
        $this->whenLoaded('teachers',function() use(&$data){
            $data['uni_code'] =  $this->teachers->uni_code;
            $data['description'] =  $this->teachers->description;
            $data['department'] = [ 
                'id' => $this->teachers->department_id,
                'name' => $this->teachers->department->name
            ];
        });
        return $data;
    }
}
