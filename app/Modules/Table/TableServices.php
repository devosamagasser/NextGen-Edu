<?php

namespace App\Modules\Table;

use App\Services\Service;
use App\Models\CourseDetail;
use App\Modules\Table\Models\Session;
use App\Modules\Table\Resources\TableResource;


class TableServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function getTable()
    {
        $sessions = Session::with(
            'course',
            'semester',
            'department',
            'hall.building',
            'postponed'
        )
        ->filter(request()->query())
        ->student()
        ->teacher()
        ->get();


        return $this->tableFormat($sessions);

    }

    public function tableFormat($sessions)
    {
        $daysOrder = [
            'saturday' => 1,
            'sunday' => 2,
            'monday' => 3,
            'tuesday' => 4,
            'wednesday' => 5,
            'thursday' => 6,
        ];
    
        return $sessions->groupBy(function ($session) {
            return $session->department->name . '-' . $session->semester->id;
        })->map(function ($group) use ($daysOrder) {
            $first = $group->first();
    
            return [
                'department' => $first->department->name,
                'department_id' => $first->department_id,
                'semester' => $first->semester->id,
                'sessions' => $group->groupBy('day')
                    ->sortBy(function ($_, $day) use ($daysOrder) {
                        return $daysOrder[strtolower($day)] ?? 999; // لو اليوم مش موجود يتحط في الآخر
                    })
                    ->map(function ($daySessions) {
                        return $daySessions->sortBy('from')->map(function ($session) {
                            return new TableResource($session);
                        })->values();
                    }),
            ];
        })->values();
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

    public function addNewSession($request, $department_id, $semester_id)
    {

        $count = count($request->course_id);
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'type' => $request->type[$i],
                'course_id' => $request->course_id[$i],
                'department_id' => $department_id,
                'semester_id' => $semester_id,
                'hall_id' => $request->hall_id[$i],
                'attendance' => $request->attendance[$i],
                'day' => $request->day[$i],
                'from' => $request->from[$i],
                'to' => $request->to[$i],
            ];
        }

        return Session::insert($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateSessionInfo($request, $department_id, $semester_id)
    {
        $this->deleteTable($department_id, $semester_id);
        $count = count($request->course_id);
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'type' => $request->type[$i],
                'course_id' => $request->course_id[$i],
                'department_id' => $department_id,
                'semester_id' => $semester_id,
                'hall_id' => $request->hall_id[$i],
                'attendance' => $request->attendance[$i],
                'day' => $request->day[$i],
                'from' => $request->from[$i],
                'to' => $request->to[$i],
            ];
        }

        return Session::insert($data);
    }

    public function deleteTable($department_id, $semester_id)
    {
        return Session::where('department_id', $department_id)
            ->where('semester_id', $semester_id)
            ->delete();
    }
}
