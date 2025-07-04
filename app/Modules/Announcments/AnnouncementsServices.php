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
    
        return Announcement::with([
            'courseDetail.teachers',
            'department',
            'semester',
            'course',
            'user',
        ])->where("post_in", '<=', now())
        ->orderBy('id', 'desc')
        ->when($user->type === 'Student', function ($q) use ($user) {
            $q->whereHas('courseDetail',function($q) use ($user) {
                $q->where('semester_id', $user->students->semester_id)
                  ->where('department_id', $user->students->department_id);
            });
        })
        ->when($user->type === 'Teacher', function ($q) use ($user) {
            $q->whereHas('courseDetail.teachers', function($q) use ($user) {
                $q->where('teacher_id', $user->teachers->id);
            });
        })
        ->filter()->paginate();
    }
    
    
    public function myAnnouncements()
    {
        $user = request()->user();
    
        return Announcement::with([
            'department',
            'semester',
            'course',
            'user',
        ])->where('user_id',$user->id)
        ->orderBy('id', 'asc')
        ->filter()
        ->paginate();
    }
    
    public function announcement(string $id)
    {
        $announcement = Announcement::findOrFail($id);    
        $this->checkAuthrization($announcement->user_id);
        return $announcement;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addNewAnnouncement($request)
    {
        $announcement = Announcement::create([
            'user_id' => request()->user()->id,
            'course_detail_id' => $request['course_id'],
            'title' => $request['title'] ?? null,
            'body' => $request['body'],
            'post_in' => isset($request['date']) ? $request['date'] .' '.  $request['time'] : now(),
        ]);
        event(new \App\Events\Announcement($announcement));
        return $announcement;
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateAnnouncementInfo($request, string $id)
    {
        $announcement = Announcement::findOrFail($id);
        
        $this->checkAuthrization($announcement->user_id);
        $data = [
            'course_detail_id' => $request->course_id ?? $announcement->course_detail_id,
            'title' => $request->title ?? $announcement->title,
            'body' => $request->body ?? $announcement->body,
        ];
        if($request->filled('date')){
            if($announcement->post_in < now()){
                throw new AccessDeniedHttpException('you can not update this announcement time because it is already published');
            }
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
