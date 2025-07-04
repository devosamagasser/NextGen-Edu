<?php

namespace App\Listeners;

use App\Events\QuizCreated;
use App\Events\MaterialCreated;
use App\Events\AssignmentCreated;
use App\Modules\Announcments\Announcement;
use App\Modules\Announcments\AnnouncementsServices;

class CreateAnnouncementListener
{

    public function __construct(private AnnouncementsServices $announcementsServices){}
    /**
     * Handle the event.
     */
    public function handle($event)
    {
        $announcement = $this->eventHandler($event);
        $this->announcementsServices->addNewAnnouncement($announcement);
    }


    public function eventHandler($event){
        $announcement = [];

        if ($event instanceof QuizCreated) {
            $quiz = $event->quiz;
            $announcement = [
                'title' => '📘 اختبار جديد: ' . $quiz->title,
                'body' => "تم جدولة اختبار جديد بعنوان \"{$quiz->title}\".\n\n"
                        . "🗓️ التاريخ: " . $quiz->date . "\n"
                        . "⏰ وقت البدء: " . $quiz->start_time . "\n"
                        . "⏳ المدة: " . $quiz->duration . " دقيقة\n"
                        . "📍 يُرجى الاستعداد جيدًا قبل موعد الاختبار.",
                'course_id' => $quiz->course_detail_id,
            ];
        }

        elseif ($event instanceof AssignmentCreated) {
            $assignment = $event->assignment;
            $announcement = [
                'title' => '📝 مهمة جديدة: ' . $assignment->title,
                'body' => "تم نشر مهمة جديدة بعنوان \"{$assignment->title}\".\n\n"
                        . "📅 تاريخ النشر: " . $assignment->created_at->format('Y/m/d') . "\n"
                        . "🧾 الوصف: " . ($assignment->description ?? 'لا يوجد وصف متاح.') . "\n"
                        . "🕔 الموعد النهائي: " . $assignment->deadline?->format('Y/m/d - h:i A') . "\n"
                        . "📌 يُرجى تسليم المهمة قبل الموعد النهائي.",
                'course_id' => $assignment->course_detail_id,
            ];
        }

        elseif ($event instanceof MaterialCreated) {
            $announcement = [
                'title' => '📚 تم رفع مواد دراسية جديدة',
                'body' => "تمت إضافة مواد دراسية جديدة للمقرر\n📌 يُرجى مراجعة قسم المواد الدراسية للاطلاع عليها.",
                'course_id' => $event->course_detail_id,
            ];
        }

        return $announcement;
    }
} 