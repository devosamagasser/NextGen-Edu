<?php

namespace App\Modules\Courses;

use App\Services\Service;
use App\Models\CourseDetail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Exception\UnexpectedValueException;

class CoursesServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function getAllCourses()
    {
        return Course::filter(request()->query())
            ->simplePaginate();
    }

    /**
     * Display the specified resource.
     */
    public function getCourseById(string $id)
    {
        $rawCourse = DB::table('courses')
            ->join('course_details', 'courses.id', '=', 'course_details.course_id')
            ->join('departments', 'course_details.department_id', '=', 'departments.id')
            ->join('semesters', 'course_details.semester_id', '=', 'semesters.id')
            ->leftJoin('course_teachers', 'course_details.id', '=', 'course_teachers.course_details_id')
            ->leftJoin('teachers', 'course_teachers.teacher_id', '=', 'teachers.id')
            ->leftJoin('users', 'teachers.user_id', '=', 'users.id')
            ->select(
                'courses.id as course_id',
                'courses.name as course_name',
                'courses.code as course_code',
                'courses.description as course_description',
                'departments.id as department_id',
                'departments.name as department_name',
                'semesters.id as semester_id',
                'semesters.name as semester_name',
                'teachers.id as teacher_id',
                'users.name as teacher_name'
            )
            ->where('courses.id', $id)
            ->get();
    
        return $this->handelCourseResponse($rawCourse);
    }
    
    private function handelCourseResponse($rawCourse)
    {
        if ($rawCourse->isEmpty()) {
            throw new ModelNotFoundException('Course not found');
        }
    
        $course = [
            'id' => $rawCourse[0]->course_id,
            'name' => $rawCourse[0]->course_name,
            'code' => $rawCourse[0]->course_code,
            'description' => $rawCourse[0]->course_description,
            'details' => []
        ];
        $detailsMap = [];
        
        foreach ($rawCourse as $row) {
            $detailKey = $row->department_id . '-' . $row->semester_id;
    
            if (!isset($detailsMap[$detailKey])) {
                $detailsMap[$detailKey] = [
                    'department_id' => $row->department_id,
                    'department_name' => $row->department_name,
                    'semester' => $row->semester_id,
                    'semester_name' => $row->semester_name,
                    'teachers' => []
                ];
            }
            
            // Only add teacher if present
            if ($row->teacher_id && $row->teacher_name) {
                $detailsMap[$detailKey]['teachers'][] = [
                    'id' => $row->teacher_id,
                    'name' => $row->teacher_name
                ];
            }
        }
        
        $course['details'] = array_values($detailsMap);
        return $course;
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

                $this->storeCourseDetails($request, $course->id);
            });
            return $course;
        } catch (UniqueConstraintViolationException $e){
            throw new UnexpectedValueException;
        }
    }

    public function storeCourseDetails($request,$courseId)
    {
        foreach($request->details as $detail){
            $course = CourseDetail::updateOrCreate([
                'course_id' => $courseId,
                'department_id' => $detail['department'],
            ],[
                'semester_id' => $detail['semester'],
            ]);
            if (isset($detail['teachers'])) {
                $course->teachers()->sync($detail['teachers']);
            } 
        }
        return true;
    }

    public function updateCourseDetails($request,$courseId)
    {
        CourseDetail::where('course_id', $courseId)->delete();
        $this->storeCourseDetails($request, $courseId);
        return true;
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

                $this->updateCourseDetails($request, $id);
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
