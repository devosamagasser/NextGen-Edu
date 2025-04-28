<?php

namespace App\Modules\Courses;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\CourseDetail;
use App\Modules\Courses\Resources\CourseResource;
use App\Modules\Courses\Validation\CourseStoreRequest;
use App\Modules\Courses\Validation\CourseDetailRequest;
use App\Modules\Courses\Validation\CourseUpdateRequest;

class CoursesController extends Controller
{
    public function __construct(public CoursesServices $courseServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = $this->courseServices->getAllCourses();
        return ApiResponse::success(CourseResource::collection($courses));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseStoreRequest $request)
    {
        $course = $this->courseServices->addNewCourse($request);
        return ApiResponse::created($course);
    }

    public function storeDetails(CourseDetailRequest $request, string $id)
    {
        $data = $this->courseServices->storeCourseDetails($request, $id);
        CourseDetail::insert($data);
        return ApiResponse::created($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $course = $this->courseServices->getCourseById($id);
        return ApiResponse::success(new CourseResource($course));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseUpdateRequest $request, string $id)
    {
        $course = $this->courseServices->updateCourseInfo($request, $id);
        return ApiResponse::updated($course);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->courseServices->deleteCourse($id);
        return ApiResponse::deleted();
    }
}
