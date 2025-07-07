<?php

namespace App\Modules\Students;

use App\Facades\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Modules\Students\CourseResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Modules\Students\Validations\StudentStoreRequest;
use App\Modules\Students\Validations\StudentUpdateRequest;

class StudentsController extends Controller
{
    public function __construct(public StudentsServices $studentsServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = $this->studentsServices->getAllStudents();
        return ApiResponse::success(StudentResource::collection($students));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentStoreRequest $request)
    {
        $student = $this->studentsServices->addNewStudent($request);
        return ApiResponse::created($student);
        // return ApiResponse::created(new StudentResource($student));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = $this->studentsServices->getStudentById($id);
        return ApiResponse::success(new StudentResource($student));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StudentUpdateRequest $request, string $id)
    {
        $student = $this->studentsServices->updateStudentInfo($request, $id);
            return Http::withHeaders([
                "Content-Type" => "application/json",
                'Authorization' => 'Bearer kfxuzk1pQESIimcee9rivOXGttoHiC8IlXaBFxhc3Y',
            ])->post('https://ngu-question-hub.azurewebsites.net/users/update', [
                'user' => [
                    'id' => $student->user->id,
                    'name' => $student->user->name,
                    'email' => $student->user->email,
                    'type' => $student->user->type,
                    'avatar' => $student->user->avatar_url
                ]
            ]);
        return ApiResponse::updated(new StudentResource($student));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->studentsServices->deleteStudent($id);
        return ApiResponse::deleted();
    }

    public function myCourses()
    {
        $courses = $this->studentsServices->myCourses();
        return ApiResponse::success(CourseResource::collection($courses));
    }

    public function studentsCourse($course_id)
    {
        $students = $this->studentsServices->getStudentsByCourse($course_id);
        return ApiResponse::success(StudentResource::collection($students));
    }

    public function export()
    {
        return $this->studentsServices->export();
        // return ApiResponse::message('successfully exported');
    }

    public function import(Request $request)
    {
        try {
            $this->studentsServices->import($request);
            return ApiResponse::success('successfully imported');
        } catch (ModelNotFoundException $e) {
           return ApiResponse::notFound($e->getMessage());
        }
    }


    public function attendance($hall_id, $student_id)
    {
        $this->studentsServices->attendance($hall_id, $student_id);
        return ApiResponse::message('Attendance recorded successfully for student with ID: ' . $student_id);
    }

}
