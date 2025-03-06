<?php

namespace App\Modules\Courses;

use App\Models\CourseDetail;
use App\Services\Service;
use http\Exception\UnexpectedValueException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class CoursesServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function getAllCourses()
    {
        return Course::with('departments','semesters','teachers')
            ->filter(request()->query())
            ->simplePaginate();
    }

    /**
     * Display the specified resource.
     */
    public function getCourseById(string $id)
    {
        return Course::with('departments','semesters','teachers')
            ->findOrFail($id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addNewCourse($request)
    {
        try {
            $course = null;
            DB::transaction(function () use ($request, &$course) {
                 $course = Course::create([
                    'name' => $request->name,
                    'code' => $request->code,
                    'description' => $request->description,
                 ]);

                $details = $this->storeCourseDetails($request, $course->id);
                CourseDetail::insert($details);
            });
            return $course;
        } catch (UniqueConstraintViolationException $e){
            throw new UnexpectedValueException;
        }
    }

    private function storeCourseDetails($request,$courseId)
    {
        $details = [];
        for ($i = 0; $i < count($request->departments); $i++) {
            if (isset($request->teachers[$i])) {
                foreach ($request->teachers[$i] as $teacherId){
                    $details[] = [
                        'course_id' => $courseId,
                        'department_id' => $request->departments[$i],
                        'semester_id' => $request->semesters[$i],
                        'teacher_id' => $teacherId
                    ];
                }
            } else {
                $details[] = [
                    'course_id' => $courseId,
                    'department_id' => $request->departments[$i],
                    'semester_id' => $request->semesters[$i],
                ];
            }
        }
        return $details;
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateCourseInfo($request, $id)
    {
        try {
            $course = Course::findOrFail($id);
            DB::transaction(function () use ($request, $id,&$course) {
                $course->update([
                    'name' => $request->name,
                    'code' => $request->code,
                    'description' => $request->description,
                ]);

                $details = $this->storeCourseDetails($request, $course);
                $course->departments()->sync($details);

            });
            return $course;
        }  catch (UniqueConstraintViolationException $e){
            throw new UnexpectedValueException;
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteCourse(string $id)
    {
        Course::findOrFail($id)->delete();
    }
}
