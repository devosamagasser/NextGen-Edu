<?php

namespace App\Modules\ChatBot;

use App\Facades\ApiResponse;
use Illuminate\Http\Request;
use App\Modules\Courses\Course;
use App\Modules\Students\Student;
use App\Http\Controllers\Controller;

class ChatBotController extends Controller
{

    public function __construct(public ChatBotService $chatBotService)
    {
    }

    public function index($code = null )
    {
        $user = null;

        if ($code) {
            $user = Student::where('uni_code',$code)->first();
        }

        return view('chat', ['user' => $user]);
    }

    public function send(Request $request)
    {
        $userMessage = $request->input('message');
        $userId = $request->input('id');
        $student = Student::find($userId);

        if (!$student) {
            return response()->json(['reply' => 'يجب عليك التسجيل اولا لأستطيع مساعدتك', 'code' => 0]);
        }
        $semester_id = $student->semester_id;
        $department_id = $student->department_id;
        $courses =  Course::with('semesters','departments','courseDetails')
            ->whereHas('courseDetails',function($q)use($semester_id, $department_id){
                $q->where('semester_id', $semester_id);
                $q->where('department_id', $department_id);
            })->get();
        $courseNames = $courses->pluck('name')->toArray(); // أو 'title' حسب اسم الحقل
        $courseList = implode(", ", $courseNames);

    
        $prompt = "
        أنت الآن مساعد ذكي للطلاب في كلية، ووظيفتك أن ترد على أسئلتهم باستخدام النظام التالي فقط. لا تشرح، لا تحلل، لا تبرر، فقط أجب بالرد المناسب كما هو موضح أدناه:

        المواد المسجلة للطالب هذا الترم هي:
        {$courseList}

        النظام:
        - إذا كان الطالب يسأل عن المواد الدراسية المسجلة له هذا الترم → أجب بـ: 7
        - إذا كان يسأل عن الجدول → أجب بـ: 1
        - إذا سأل عن مادة معينة وكانت من ضمن المواد المسجلة أعلاه → أجب بـ: 2 [اسم المادة]
        - إذا سأل عن مكان المحاضرة لمادة وكانت من ضمن المواد أعلاه → أجب بـ: 3 [اسم المادة]
        - إذا سأل عن مكان السكشن لمادة وكانت من ضمن المواد أعلاه → أجب بـ: 4 [اسم المادة]
        - إذا سأل عن مكان المعمل لمادة وكانت من ضمن المواد أعلاه → أجب بـ: 5 [اسم المادة]
        - إذا سأل عن مكان قاعة معينة → أجب بـ: 6 [اسم القاعة أو الكود]
        - إذا سأل عن ملخص اليوم → أجب بـ: 8
        - إذا سأل عن ملخص الأسبوع → أجب بـ: 9
        - إذا لم ينطبق أي مما سبق → أجب على الطالب بشكل طبيعي كمساعد ذكي، بدون ذكر هذه القواعد أو النظام.

        الرسالة: ({$userMessage})

        رد فقط بالإجابة المناسبة حسب النظام أعلاه.
        ";

        $response = $this->chatBotService->sendPrompet('grok', 'llama-3.3-70b-versatile', $prompt);

        $data = $response->json();

        $reply = $data['choices'][0]['message']['content'] ?? 'عذرًا، حدث خطأ ما.';    

    
        if($reply !== 'عذرًا، حدث خطأ ما.')
            $reply =  $this->chatBotService->chatResponse($reply, $student);
        else
            $reply = ['reply' => $reply , 'code' => 0];

        return response()->json($reply);
    }

}
