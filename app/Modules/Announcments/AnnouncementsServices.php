<?php

namespace App\Modules\Announcments;

use App\Models\CourseDetail;
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
        $user = request()->user();
    
        $query = Announcement::with([
            'department',
            'semester',
            'course:id,name',
            'user', 
        ])->where("post_in", '<=', now())
        ->orderBy('id', 'desc')
        ->filter();

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
            $teacher = Teacher::with(['departments','semesters','courses'])->where('user_id',$user->id)->first();
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
        $CourseDetail = CourseDetail::findOrFail($request->course_id);
        $data = [
            'user_id' => $request->user()->id,
            'department_id' => $CourseDetail->department_id,
            'semester_id' => $CourseDetail->semester_id,
            'course_id' => $CourseDetail->course_id,
            'title' => $request->title ?? null,
            'body' => $request->body,
        ];
        if($request->filled('date')){
            $data['post_in'] = $request->date .' '.  $request->time;
        }else{
            $data['post_in'] = now();
        }
        return Announcement::create($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateAnnouncementInfo($request, string $id)
    {
        $announcement = Announcement::findOrFail($id);
        $CourseDetail = CourseDetail::findOrFail($request->course_id);

        $this->checkAuthrization($announcement->user_id);
        $data = [
            'department_id' => $CourseDetail->department_id ?? $announcement->department_id,
            'semester_id' => $CourseDetail->semester_id ?? $announcement->semester_id,
            'course_id' => $CourseDetail->course_id ?? $announcement->course_id,
            'title' => $request->title ?? $announcement->title,
            'body' => $request->body ?? $announcement->body,
        ];

        if($request->filled('date')){
            $data['post_in'] = $request->date .' '.  $request->time;
        }

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
    public function deleteAnnouncement(string $id)
    {
        $announcement = Announcement::findOrFail($id);
        $this->checkAuthrization($announcement->user_id);
        $announcement->delete();
    }


    public function checkAuthrization($id)
    {
        $userId = request()->user()->id;
        if($id != $userId){
            throw new AccessDeniedHttpException('forbidden');
        }
    }
}
