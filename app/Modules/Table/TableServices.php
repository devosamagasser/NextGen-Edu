<?php

namespace App\Modules\Table;

use App\Services\Service;
use App\Models\CourseDetail;
use App\Modules\Table\Models\Session;


class TableServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function getTable()
    {
        return Session::with(
            'details.course',
            'details.teacher.user', 
            'details.semester', 
            'details.department',
            'hall.building'
        )->filter(request()->query())
            ->get();
    }

    /**
     * Display the specified resource.
     */
    public function getTeacherTable()
    {
        return Session::with('details')
            ->teacher()
            ->get();
    }

    public function getStudentTable()
    {
        return Session::with('details')
            ->student()
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addNewSession($request)
    {
        return Session::create([
            'type' => $request->type,
            'course_detail_id' => $this->getCourseDetailId($request),
            'hall_id' => $request->hall_id,
            'attendance' => $request->attendance,
            'day' => $request->day,
            'date' => $request->date,
            'from' => $request->from,
            'to' => $request->to,
            'week' => 0,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateSessionInfo($request, $id)
    {
        $session = Session::findOrfail($id);
        $data = $request->except('course_id', 'semester_id', 'department_id', 'teacher_id');
        $data = array_merge($data, ['course_detail_id' => $this->getCourseDetailId($request, $session)]);
        $data = $this->updatedDataFormated($request, $data);
        $session->fill($data);
        return ($session->isDirty()) ? $session : false;
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function deleteTeacher(string $id)
    // {
    //     $userId = Teacher::findOrfail($id)->user_id;
    //     return User::findOrfail($userId)->delete();
    // }

    public function getCourseDetailId($request, $session = null)
    {
        $course_id = $request->course_id ?? $session->details->course_id;
        $department_id = $request->department_id ?? $session->details->department_id;

        return CourseDetail::where('course_id', $course_id)
            ->where('department_id', $department_id)
            ->firstOrFail()->id;
    }
}
