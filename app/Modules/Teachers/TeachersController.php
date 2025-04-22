<?php

namespace App\Modules\Teachers;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Courses\Resources\SemesterResource;
use App\Modules\Courses\Resources\DepartmentResource;
use App\Modules\Teachers\Validation\TeacherStoreRequest;
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
