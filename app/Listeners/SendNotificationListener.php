<?php

namespace App\Listeners;

use App\Events\QuizCreated;
use Illuminate\Support\Str;
use App\Models\CourseDetail;
use App\Events\MaterialCreated;
use App\Events\AssignmentCreated;
use App\Modules\Students\Student;
use Illuminate\Support\Facades\Http;
use App\Modules\Announcments\Announcement;
use App\Modules\Announcments\AnnouncementsServices;

class SendNotificationListener
{

    public function __construct(){}
    /**
     * Handle the event.
     */
    public function handle($event)
    {
        return Http::withHeaders([
            "Content-Type" => "application/json",
            'Authorization' => 'Bearer kfxuzk1pQESIimcee9rivOXGttoHiC8IlXaBFxhc3Y',
        ])->post('https://ngu-question-hub.azurewebsites.net/notification/announ', [
            'body' => Str::limit($event->announcement->body, 100),
            'senderName' => request()->user()->name,
            'ids' => $this->getReceivers($event),
            'type' => 'announ',
        ]);
    }


    public function getReceivers($event)
    {
        $courseDetailId = $event->announcement->course_detail_id;
    
        $receivers = Student::query()
            ->join('course_details', function ($join) use ($courseDetailId) {
                $join->on('students.semester_id', '=', 'course_details.semester_id')
                     ->on('students.department_id', '=', 'course_details.department_id')
                     ->where('course_details.id', '=', $courseDetailId);
            })
            ->pluck('students.user_id');
    
        return $receivers;
    }


} 