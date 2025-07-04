<?php

namespace App\Modules\Courses\Resources;

use App\Modules\Departments\DepartmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        // $details = $this->detailsHandler();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
        ];
    }


    // public function detailsHandler()
    // {
    //     $departments = DepartmentResource::collection($this->departments);
    //     $semesters = SemesterResource::collection($this->semesters);
    //     $details = [];
    //     $departments->each(function ($teacher,$index) use($semesters,$departments,&$details){
    //         $department = $departments->get($index);
    //         $departmentId = $department->id;
    //         $departmentName = $department->name;
            
    //         if (!isset($details[$departmentName])) {
    //             $details[$departmentName] = [
    //                 'department_id' => $departmentId,
    //                 'department_name' => $department->name,
    //                 'semester' => $semesters->get($index)->id,
    //                 'semester_name' => $semesters->get($index)->name,
    //                 'teachers' => [$teacher]
    //             ];
    //         }else{
    //             $details[$departmentName]['teachers'][] = $teacher;
    //         }
    //     });
    //     return array_values($details);
    // }

}
