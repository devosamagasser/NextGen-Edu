<?php

namespace App\Modules\Announcments;

use App\Services\Service;
use App\Modules\Teachers\Teacher;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AnnouncementsServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function getAllAnnouncements()
    {
        $user = request()->user()->load(['teachers', 'students']);
    
        $query = Announcement::with([
            'department',
            'semester',
            'course:id,name',
            'user', 
        ]);
    
        if ($user->type === 'Student') {
            $query->where(function ($q) use ($user) {
                $q->where('department_id', $user->students->department_id)
                  ->orWhereNull('department_id');
            })->where(function ($q) use ($user) {
                $q->where('semester_id', $user->students->semester_id)
                  ->orWhereNull('semester_id');
            });
        }
    
        if ($user->type === 'Teacher') {
            $teacher = Teacher::with(['departments','semesters','courses'])->find($user->id);
            $semesterIds = $teacher->semesters->pluck('id')->toArray();
            $departmentsIds = $teacher->departments->pluck('id')->toArray();
            $query->whereIn('semester_id', $semesterIds)
                  ->whereIn('department_id', $departmentsIds)
                  ->orWhereNull('department_id');
        }
        
        return $query->paginate();
    }
    
    
    public function myAnnouncements()
    {
        $user = request()->user();
    
        $query = Announcement::with([
            'department',
            'semester',
            'course:id,name',
            'user', 
        ])->where('user_id',$user->id);
    
        return $query->paginate();
    }
    
    public function announcement(string $id)
    {
        $user = request()->user();
        $announcement = Announcement::findOrFail($id);    
        $this->checkAuthrization($announcement->user_id);
        return $announcement;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addNewAnnouncement($request)
    {
        $data = [
            'user_id' => $request->user()->id,
            'department_id' => $request->department_id ?? null,
            'semester_id' => $request->semester_id ?? null,
            'course_id' => $request->course_id ?? null,
            'title' => $request->title ?? null,
            'body' => $request->body,
            'time_to_post' => $request->time_to_post ?? now()->format('Y-m-d'),
            'time' => $request->time ?? now()->format('H:i:s')
        ];
        return Announcement::create($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateAnnouncementInfo($request, string $id)
    {
        $announcement = Announcement::findOrFail($id);
        $this->checkAuthrization($announcement->user_id);
        $data = [
            'department_id' => $request->department_id ?? $announcement->department_id,
            'semester_id' => $request->semester_id ?? $announcement->semester_id,
            'course_id' => $request->course_id ?? $announcement->course_id,
            'title' => $request->title ?? $announcement->title,
            'body' => $request->body ?? $announcement->body,
            'time_to_post' => $request->time_to_post ??  $announcement->time_to_post,
            'time' => $request->time ??  $announcement->time
        ];
        $announcement->fill($data);
        if($announcement->isDirty()){
            $announcement->save();
            return $announcement;
        }
        return false;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteBuilding(string $id)
    {
        $announcement = Announcement::findOrFail($id);
        $this->checkAuthrization($announcement->user_id);
        $announcement->delete();
    }


    public function checkAuthrization($id)
    {
        $userId = request()->user()->id;
        if($id != $userId){
            throw new AccessDeniedHttpException();
        }
    }
}
