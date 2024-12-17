<?php

namespace App\Services\Dashboard;

use App\Facades\ApiResponse;
use App\Http\Resources\Courses\CourseResource;
use App\Models\Course;
use App\Models\CourseDetail;
use App\Services\Service;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\MockObject\Generator\DuplicateMethodException;

class CoursesServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $courses = Course::with('departments','semesters','teachers')->filter(request()->query())->simplePaginate(1);
            return ApiResponse::success(CourseResource::collection($courses));
        } catch (Exception $e) {
            return ApiResponse::serverError();
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $course = Course::with('departments','semesters','teachers')->findOrFail($id);
            return ApiResponse::success(new CourseResource($course));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Course not found');

        } catch (Exception $e) {
            return ApiResponse::serverError();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
//        dd($request->all());
        try {
            DB::beginTransaction();
             $course = Course::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
            ]);

            $details = [];
            for ($i = 0; $i < count($request->departments); $i++) {
                if (isset($request->teachers[$i])) {
                    foreach ($request->teachers[$i] as $teacherId){
                        $details[] = [
                            'course_id' => $course->id,
                            'department_id' => $request->departments[$i],
                            'semester_id' => $request->semesters[$i],
                            'teacher_id' => $teacherId
                        ];
                    }
                } else {
                    $details[] = [
                        'course_id' => $course->id,
                        'department_id' => $request->departments[$i],
                        'semester_id' => $request->semesters[$i],
                    ];
                }
            }

            CourseDetail::insert($details);

            DB::commit();
            return ApiResponse::created($course);


        }catch (UniqueConstraintViolationException $e){
            DB::rollBack();
            return ApiResponse::message('You can\'t duplicate course details',Response::HTTP_BAD_REQUEST);

        } catch (Exception $e){
            DB::rollBack();
            return ApiResponse::serverError();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        try {
            $course = Course::findOrFail($id);
            DB::beginTransaction();
            $course->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
            ]);

            $details = [];
            for ($i = 0; $i < count($request->departments); $i++) {
                if ($request->teachers && $request->teachers[$i] && $teacherCount = count($request->teachers[$i])) {
                    for ($j = 0; $j < $teacherCount; $j++) {
                        $details[] = [
                            'course_id' => $id,
                            'department_id' => $request->departments[$i],
                            'semester_id' => $request->semesters[$i],
                            'teacher_id' => $request->teachers[$i][$j]
                        ];
                    }
                } else {
                    $details[] = [
                        'course_id' => $id,
                        'department_id' => $request->departments[$i],
                        'semester_id' => $request->semesters[$i],
                    ];
                }
            }

            $course->departments()->sync($details);
            DB::commit();
            return ApiResponse::updated($course);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return ApiResponse::notFound('Course not found');

        } catch (UniqueConstraintViolationException $e){
            DB::rollBack();
            return ApiResponse::message('You can\'t duplicate course details',Response::HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse::serverError();
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $course = Course::findOrFail($id);
            $course->delete();
            return ApiResponse::message('Deleted successfully');

        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Course not found');

        } catch (Exception $e) {
            return ApiResponse::serverError();

        }
    }
}
