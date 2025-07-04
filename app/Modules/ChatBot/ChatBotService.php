<?php

namespace App\Modules\ChatBot;

use App\Modules\Courses\Course;
use App\Http\Controllers\Controller;
use App\Models\CourseDetail;
use App\Modules\Table\TableServices;
use Illuminate\Support\Facades\Http;
use App\Modules\Table\Models\Session;

class ChatBotService extends Controller
{

    public $tokens;

    public function __construct()
    {
        $this->tokens['grok'] = [
                'token' => env('GROK_API_KEY'),
                'url' => 'https://api.groq.com/openai/v1/chat/completions',
            ];
    }


    public function sendPrompet($provider, $model, $prompt, $timeout = 120)
    {
        if (!isset($this->tokens[$provider])) {
            return response()->json(['error' => 'Invalid provider.']);
        }

        return Http::timeout($timeout)->withHeaders([
            "Content-Type" => "application/json",
            'Authorization' => 'Bearer ' . $this->tokens[$provider]['token'],
        ])->post($this->tokens[$provider]['url'], [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ],
            ],
        ]);
    }


    public function chatResponse($reply, $student)
    {
        $parts = explode(' ', $reply);
        $message = trim($reply);

        // Detect intent
        if (preg_match('/(محاضرة|سكشن|معمل) (.+)/u', $message, $matches)) {
            $type = $matches[1];
            $subject = $matches[2];
            return $this->getLocationOfSubject($subject, $type, $student);
        }
        if (preg_match('/قاعة (.+)/u', $message, $matches)) {
            $hallName = $matches[1];
            return $this->getHallLocation($hallName);
        }
        if (preg_match('/ملخص اليوم/u', $message)) {
            return $this->getTodaySummary($student);
        }
        if (preg_match('/ملخص الأسبوع/u', $message)) {
            return $this->getWeekSummary($student);
        }

        $responseData = [
            1 => fn($student) => $this->getTable($student),
            2 => fn($student) => $this->getCourseDetails($parts[1] ?? '', $student),
            3 => fn($student) => $this->getLocationOfSubject($parts[1] ?? '', 'lecture', $student),
            4 => fn($student) => $this->getLocationOfSubject($parts[1] ?? '', 'section', $student),
            5 => fn($student) => $this->getLocationOfSubject($parts[1] ?? '', 'lab', $student),
            6 => fn($student) => $this->getHallLocation($parts[1] ?? ''),
            7 => fn($student) => $this->getMycourses($student),
            8 => fn($student) => $this->getTodaySummary($student),
            9 => fn($student) => $this->getWeekSummary($student),
        ];

        $code = $parts[0] ?? '0';

        if (isset($responseData[$code])) {
            return $responseData[$code]($student);
        }

        return ['reply' => $reply,'code' => 0];
    }


    public function getMycourses($student)
    {
        $semester_id = $student->semester_id;
        $department_id = $student->department_id;
        $courses =  Course::with('semesters','departments','courseDetails')
            ->whereHas('courseDetails',function($q)use($semester_id, $department_id){
                $q->where('semester_id', $semester_id);
                $q->where('department_id', $department_id);
            })->get();

        return [
            'reply' => $courses,
            'code' => 7
        ];
    }

    public function getTable($student)
    {
        $sessions = Session::with(
            'course',
            'semester',
            'department',
            'hall.building'
        )
        ->where('semester_id', $student->semester_id)
        ->where('department_id', $student->department_id)
        ->get();
        return [
            'reply' => (new TableServices())->tableFormat($sessions), 
            'code' => 1
        ];

    }


    public function getCourseDetails($courseName, $student)
    {
        $semester_id = $student->semester_id;
        $department_id = $student->department_id;

        $course = CourseDetail::with('course','announcements','teachers.user','materials','quizzes','assignments')
            ->where('semester_id', $semester_id)
            ->where('department_id', $department_id)
            ->whereHas('course', function ($q) use ($courseName) {
                $q->where('name', 'like', '%' . $courseName . '%');
            })
            ->first();

        if (!$course) {
            return ['reply' => 'المادة غير مسجلة لهذا الترم.', 'code' => 0];
        }

        return [
            'reply' => $course,
            'code' => 2
        ];
    }

    // New: Get location of lecture/section/lab for a subject
    public function getLocationOfSubject($subject, $type, $student)
    {
        // Example: Find session for subject and type (محاضرة/سكشن/معمل)
        $session = Session::with('course', 'hall.building')
            ->where('semester_id', $student->semester_id)
            ->where('department_id', $student->department_id)
            ->whereHas('course', function ($q) use ($subject) {
                $q->where('name', 'like', "%$subject%");
            })
            ->where('type', $type)
            ->first();
        if (!$session) {
            return ['reply' => 'لم يتم العثور على ' . $type . ' لهذه المادة.', 'code' => 0];
        }
        $location = $session->hall->name . ' - ' . $session->hall->building->name;
        return ['reply' => "مكان $type $subject: $location", 'code' => 10];
    }

    // New: Get location of a hall
    public function getHallLocation($hallName)
    {
        $hall = \App\Modules\Halls\Hall::with('building')->where('name', 'like', "%$hallName%") ->first();
        if (!$hall) {
            return ['reply' => 'لم يتم العثور على القاعة.', 'code' => 0];
        }
        $location = $hall->name . ' - ' . $hall->building->name;
        return ['reply' => "مكان القاعة: $location", 'code' => 11];
    }

    // New: Get today's summary for the student
    public function getTodaySummary($student)
    {
        $today = now()->toDateString();
        $dayName = now()->translatedFormat('l');
        $semester_id = $student->semester_id;
        $department_id = $student->department_id;

        // Sessions
        $sessions = Session::with('course', 'hall')
            ->where('semester_id', $semester_id)
            ->where('department_id', $department_id)
            ->whereDate('date', $today)
            ->get();

        // Get all course details for this student
        $courseDetails = CourseDetail::with(['quizzes','assignments','announcements','materials','course'])
            ->where('semester_id', $semester_id)
            ->where('department_id', $department_id)
            ->get();

        // Quizzes, Assignments, Announcements, Materials for today
        $quizzes = collect();
        $assignments = collect();
        $announcements = collect();
        $materials = collect();
        foreach ($courseDetails as $cd) {
            $quizzes = $quizzes->concat($cd->quizzes->where('date', $today));
            $assignments = $assignments->concat($cd->assignments->where('due_date', $today));
            $announcements = $announcements->concat($cd->announcements->where('created_at', '>=', $today . ' 00:00:00')->where('created_at', '<=', $today . ' 23:59:59'));
            $materials = $materials->concat($cd->materials->where('created_at', '>=', $today . ' 00:00:00')->where('created_at', '<=', $today . ' 23:59:59'));
        }

        $summary = "ملخص اليوم ($dayName):\n";

        // Sessions section
        $summary .= "\nالمحاضرات/السكاشن/المعامل:\n";
        if ($sessions->isEmpty()) {
            $summary .= "- لا يوجد لديك محاضرات أو سكاشن اليوم.\n";
        } else {
            foreach ($sessions as $session) {
                $summary .= '- ' . $session->course->name . ' في ' . $session->hall->name;
                if (isset($session->start_time)) {
                    $summary .= ' الساعة ' . $session->start_time;
                }
                $summary .= "\n";
            }
        }

        // Quizzes section
        $summary .= "\nالاختبارات:\n";
        if ($quizzes->isEmpty()) {
            $summary .= "- لا يوجد اختبارات اليوم.\n";
        } else {
            foreach ($quizzes as $quiz) {
                $summary .= '- ' . $quiz->title . "\n";
            }
        }

        // Assignments section
        $summary .= "\nالواجبات:\n";
        if ($assignments->isEmpty()) {
            $summary .= "- لا يوجد واجبات للتسليم اليوم.\n";
        } else {
            foreach ($assignments as $assignment) {
                $summary .= '- ' . $assignment->title . "\n";
            }
        }

        // Announcements section
        $summary .= "\nالإعلانات:\n";
        if ($announcements->isEmpty()) {
            $summary .= "- لا يوجد إعلانات جديدة اليوم.\n";
        } else {
            foreach ($announcements as $announcement) {
                $summary .= '- ' . $announcement->title . "\n";
            }
        }

        // Materials section
        $summary .= "\nالمواد المضافة:\n";
        if ($materials->isEmpty()) {
            $summary .= "- لا يوجد مواد مضافة اليوم.\n";
        } else {
            foreach ($materials as $material) {
                $summary .= '- ' . $material->title . "\n";
            }
        }

        return ['reply' => $summary, 'code' => 12];
    }

    // New: Get week's summary for the student
    public function getWeekSummary($student)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $semester_id = $student->semester_id;
        $department_id = $student->department_id;

        // Sessions grouped by day
        $sessions = Session::with('course', 'hall')
            ->where('semester_id', $semester_id)
            ->where('department_id', $department_id)
            ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->get()
            ->groupBy(function($item) { return \Carbon\Carbon::parse($item->date)->translatedFormat('l'); });

        // Get all course details for this student
        $courseDetails = CourseDetail::with(['quizzes','assignments','announcements','materials','course'])
            ->where('semester_id', $semester_id)
            ->where('department_id', $department_id)
            ->get();

        // Quizzes, Assignments, Announcements, Materials for the week grouped by day
        $quizzesByDay = [];
        $assignmentsByDay = [];
        $announcementsByDay = [];
        $materialsByDay = [];
        foreach ($courseDetails as $cd) {
            foreach ($cd->quizzes->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()]) as $quiz) {
                $day = \Carbon\Carbon::parse($quiz->date)->translatedFormat('l');
                $quizzesByDay[$day][] = $quiz->title;
            }
            foreach ($cd->assignments->whereBetween('due_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()]) as $assignment) {
                $day = \Carbon\Carbon::parse($assignment->due_date)->translatedFormat('l');
                $assignmentsByDay[$day][] = $assignment->title;
            }
            foreach ($cd->announcements->whereBetween('created_at', [$startOfWeek->toDateString() . ' 00:00:00', $endOfWeek->toDateString() . ' 23:59:59']) as $announcement) {
                $day = \Carbon\Carbon::parse($announcement->created_at)->translatedFormat('l');
                $announcementsByDay[$day][] = $announcement->title;
            }
            foreach ($cd->materials->whereBetween('created_at', [$startOfWeek->toDateString() . ' 00:00:00', $endOfWeek->toDateString() . ' 23:59:59']) as $material) {
                $day = \Carbon\Carbon::parse($material->created_at)->translatedFormat('l');
                $materialsByDay[$day][] = $material->title;
            }
        }

        $summary = "ملخص الأسبوع:\n";
        $days = ['السبت','الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة'];
        foreach ($days as $day) {
            $summary .= "\n[$day]\n";
            // Sessions
            $summary .= "المحاضرات/السكاشن/المعامل:\n";
            if (isset($sessions[$day]) && count($sessions[$day])) {
                foreach ($sessions[$day] as $session) {
                    $summary .= '- ' . $session->course->name . ' في ' . $session->hall->name;
                    if (isset($session->start_time)) {
                        $summary .= ' الساعة ' . $session->start_time;
                    }
                    $summary .= "\n";
                }
            } else {
                $summary .= "- لا يوجد محاضرات أو سكاشن.\n";
            }
            // Quizzes
            $summary .= "الاختبارات:\n";
            if (isset($quizzesByDay[$day]) && count($quizzesByDay[$day])) {
                foreach ($quizzesByDay[$day] as $quizTitle) {
                    $summary .= '- ' . $quizTitle . "\n";
                }
            } else {
                $summary .= "- لا يوجد اختبارات.\n";
            }
            // Assignments
            $summary .= "الواجبات:\n";
            if (isset($assignmentsByDay[$day]) && count($assignmentsByDay[$day])) {
                foreach ($assignmentsByDay[$day] as $assignmentTitle) {
                    $summary .= '- ' . $assignmentTitle . "\n";
                }
            } else {
                $summary .= "- لا يوجد واجبات للتسليم.\n";
            }
            // Announcements
            $summary .= "الإعلانات:\n";
            if (isset($announcementsByDay[$day]) && count($announcementsByDay[$day])) {
                foreach ($announcementsByDay[$day] as $announcementTitle) {
                    $summary .= '- ' . $announcementTitle . "\n";
                }
            } else {
                $summary .= "- لا يوجد إعلانات جديدة.\n";
            }
            // Materials
            $summary .= "المواد المضافة:\n";
            if (isset($materialsByDay[$day]) && count($materialsByDay[$day])) {
                foreach ($materialsByDay[$day] as $materialTitle) {
                    $summary .= '- ' . $materialTitle . "\n";
                }
            } else {
                $summary .= "- لا يوجد مواد مضافة.\n";
            }
        }
        return ['reply' => $summary, 'code' => 13];
    }
}
