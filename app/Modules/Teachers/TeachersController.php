<?php

namespace App\Modules\Teachers;

use App\Facades\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Modules\Departments\DepartmentResource;
use App\Modules\Courses\Resources\SemesterResource;
use App\Modules\Teachers\Validation\TeacherStoreRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Modules\Teachers\Validation\TeacherUpdateRequest;

class TeachersController extends Controller
{
    public function __construct(public TeachersServices $teachersServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = $this->teachersServices->getAllTeachers();
        return ApiResponse::success(TeacherResource::collection($teachers));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeacherStoreRequest $request)
    {
        $teacher = $this->teachersServices->addNewTeacher($request);
        return ApiResponse::created($teacher);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $teacher = $this->teachersServices->getTeacherById( $id);
        return ApiResponse::success(new TeacherResource($teacher));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeacherUpdateRequest $request, string $id)
    {
        $teacher = $this->teachersServices->updateTeacherInfo($request, $id);
        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer kfxuzk1pQESIimcee9rivOXGttoHiC8IlXaBFxhc3Y',
        ])->put('https://ngu-question-hub.azurewebsites.net/users/update', [
            'user' => [
                'id' => $teacher->user->id,
                'name' => $teacher->user->name,
                'email' => $teacher->user->email,
                'type' => $teacher->user->type,
                'avatar' => $teacher->user->avatar_url,
                'uni_code' =>  $teacher->uni_code,
                'description' =>  $teacher->description,
                'department' => [ 
                    'id' => $teacher->department_id,
                    'name' => $teacher->department->name
                ]
            ]
        ]);
        return ApiResponse::updated($teacher);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacher = $this->teachersServices->deleteTeacher($id);
        return ApiResponse::deleted($teacher);
    }

    public function import(Request $request)
    {
        try {
            $this->teachersServices->import($request);
            return ApiResponse::success('successfully imported');
        } catch (ModelNotFoundException $e) {
           return ApiResponse::notFound($e->getMessage());
        }
    }

    public function myCourses()
    {
        $courses = $this->teachersServices->myCourses();
        return ApiResponse::success(CourseResource::collection($courses));
    }

    public function myDepartments()
    {
        $departments = $this->teachersServices->myDepartments();
        return ApiResponse::success(DepartmentResource::collection($departments));
    }

    public function mySemesters()
    {
        $semesters = $this->teachersServices->mySemesters();
        return ApiResponse::success(SemesterResource::collection($semesters));
    }


}
