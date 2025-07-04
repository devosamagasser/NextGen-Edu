<?php

namespace App\Modules\Quizzes;

use Carbon\Carbon;
use App\Services\Service;
use App\Models\CourseDetail;
use Illuminate\Support\Facades\DB;
use App\Modules\Quizzes\Models\Quiz;
use App\Modules\Questions\Models\Answer;
use App\Modules\Quizzes\Models\QuizAnswer;
use App\Modules\Questions\QuestionsServices;
use App\Modules\Questions\Reaources\AnswerResource;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class QuizzesServices extends Service
{
    /**
     * Display a listing of the resource.
     */
    public function getAllQuizzes()
    {
        $user = request()->user();
    
        $quizzes = Quiz::with('courseDetail', 'course', 'department', 'semester', 'teacher.user')
            ->orderBy('id', 'desc')
            ->filter()
            ->when($user->hasRole('Teacher'), function ($query) use ($user) {
                $query->where('teacher_id', $user->teachers->id);
            })
            ->when($user->hasRole('Student'), function ($query) use ($user) {
                $query->whereHas('courseDetail', function ($q) use ($user) {
                    $q->where([
                        ['department_id', $user->students->department_id],
                        ['semester_id', $user->students->semester_id],
                    ]);
                });
            })
            ->get();
    
        $this->statusHandler($quizzes);

        return $quizzes;
    }
    
    private function statusHandler($quizzes)
    {
        $now = now();
        $quizzes->each(function ($quiz) use ($now) {
            $quizStart = Carbon::parse($quiz->date . ' ' . $quiz->start_time);
            $quizEnd = $quizStart->copy()->addMinutes($quiz->duration);
    
            if ($now->greaterThanOrEqualTo($quizEnd)) {
                $quiz->status = 'finished';
            } elseif ($now->between($quizStart, $quizEnd)) {
                $quiz->status = 'started';
            } else {
                $quiz->status = 'scheduled';
            }

            $quiz->save();
        });
    }
    

    /**
     * Display the specified resource.
     */
    public function getQuizeById($id)
    {
        return Quiz::with('questions.answers','course', 'teacher', 'teacher.user')->findOrFail($id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addNewQuize($request)
    {
        return DB::transaction(function () use($request) {
            $user = request()->user();

            $quiz = Quiz::create([
                'teacher_id' => $user->teachers->id,
                'course_detail_id' => $request->course_id,               
                'title' => $request->title,
                'description' => $request->description,
                'total_degree' => $request->total_degree,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'duration' => $request->duration,
            ]);
            
            event(new \App\Events\QuizCreated($quiz));
            $questions = $this->questionHandler($request,$quiz);
            $quiz->questions()->attach($questions);

            return $quiz;
        });
    }
    

    public function updateQuizInfo($request,string $id)
    {
        return DB::transaction(function () use($request,$id) {
            $user = request()->user();
            $quiz = Quiz::where('teacher_id',$user->teachers->id)->findOrFail($id);
            $data = [
                'course_detail_id' => $request->course_id,
                'title' => $request->title,
                'description' => $request->description,
                'total_degree' => $request->total_degree,
            ];
            if($request->filled('date') || $request->filled('start_time')){
                if($quiz->type !=  'scheduled'){
                    throw new AccessDeniedHttpException('you can not update this quiz\'s time because it is already published');
                }
                $data['date'] = $request->date;
                $data['start_time'] = $request->start_time;
            }
            $quiz->update($data);

            $quiz->questions()->detach();
            $questions = $this->questionHandler($request,$quiz);

            $quiz->questions()->attach($questions);
    
            return $quiz;
        });
    }


    public function deleteQuiz(string $id)
    {
        $user = request()->user();
        $quiz = Quiz::where('teacher_id',$user->teachers->id)->findOrFail($id);
        return $quiz->delete();
    }


    public function startStudentQuiz($quiz_id)
    {
        $user = request()->user();
        $quiz = Quiz::with('questions')->findOrFail($quiz_id);
    
        $start = Carbon::parse($quiz->date . ' ' . $quiz->start_time);
        $end = $start->copy()->addMinutes($quiz->duration);
        $now = now();
    
        if ($now->lt($start)) {
            throw new AccessDeniedHttpException('Quiz has not started yet.');
        }
        
        if ($now->gt($end)) {
            throw new AccessDeniedHttpException('Quiz is already finished.');
        }
        
        $alreadyStarted = QuizAnswer::where('quiz_id', $quiz_id)
        ->where('student_id', $user->id)
        ->exists();
        
        if ($alreadyStarted) {
            throw new AccessDeniedHttpException('You have already started this quiz.');
        }
        return $quiz;
    }


    public function submitQuizAnswers($quiz_id, $questions)
    {
        $user = request()->user();
        $studentId = $user->students->id;

        $quiz = Quiz::with('questions.answers')->findOrFail($quiz_id);

        $this->deadlineCheck($quiz);
        
        $this->submitedBeforeCheck($quiz_id, $studentId);

        // الأسئلة المعتمدة في الكويز
        $validQuestionIds = $quiz->questions->pluck('id')->toArray();

        DB::beginTransaction();
        try {
            foreach ($questions as $data) {
                $questionId = $data['question'];
                $answerId = $data['answer'];

                // التحقق إن السؤال يتبع للكويز
                if (!in_array($questionId, $validQuestionIds)) {
                    throw new \Exception("Invalid question ID: $questionId");
                }

                // التحقق من صحة الإجابة
                $answer = Answer::where('id', $answerId)
                    ->where('question_id', $questionId)
                    ->first();

                if (!$answer) {
                    throw new \Exception("Invalid answer ID: $answerId for question: $questionId");
                }

                // حساب الدرجة
                $isCorrect = $answer->is_correct;
                $degree = $isCorrect ? $quiz->questions->firstWhere('id', $questionId)->pivot->degree : 0;

                QuizAnswer::create([
                    'quiz_id' => $quiz_id,
                    'question_id' => $questionId,
                    'answer_id' => $answerId,
                    'student_id' => $studentId,
                    'degree' => $degree,
                ]);
            }

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \Exception("Failed to submit quiz answers: " . $e->getMessage());
        }
    }


    public function getQuizWithStudentAnswers($id, $studentId = null)
    {
        if (is_null($studentId)) {
            $user = request()->user();
            $studentId = $user->students->id;
        }

        $quiz = Quiz::with('questions.answers')->findOrFail($id);

        $studentAnswers = QuizAnswer::with('question')
            ->where('quiz_id', $id)
            ->where('student_id', $studentId)
            ->get(); 

        $totalDegree = $studentAnswers->sum('degree');
        
        $data = [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'description' => $quiz->description,
            'max_degree' => $quiz->total_degree,
            'student_degree' => $totalDegree,
            'date' => $quiz->date, 
            'start_time' => $quiz->start_time, 
            'duration' => $quiz->duration,
            'questions' => []
        ];

        foreach ($quiz->questions as $question) {
            $data['questions'][] = [
                'id' => $question->id,
                'question' => $question->question,
                'answers' => AnswerResource::collection($question->answers),
                'student_answer' => optional($studentAnswers->where('question_id', $question->id)->first())->answer_id,
            ];
        }

        return $data;
    }


    public function getQuizStudentsAnswers($id)
    {
        $quiz = Quiz::with('questions.answers')->findOrFail($id);

        $students = QuizAnswer::with(['student', 'question', 'question.answers'])
            ->where('quiz_id', $id)
            ->get()
            ->groupBy('student_id');

        $result = [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'description' => $quiz->description,
            'max_degree' => $quiz->total_degree,
            'date' => $quiz->date, 
            'start_time' => $quiz->start_time, 
            'duration' => $quiz->duration,
            'students' => []
        ];

        foreach ($students as $studentId => $answers) {
            $student = $answers->first()->student;

            $result['students'][] = [
                'student_id' => $student->id,
                'student_name' => $student->user->name ?? '',
                'avatar' => $student->user->avatar_url ?? '',
                'degree' => $answers->sum('degree'),
            ];
        }

        return $result;
    }


    private function questionHandler($request,$quiz)
    {
        $questions = [];
        if ($request->filled('old_questions')) {
            foreach ($request->old_questions as $old_question) {
                $questions[] = [
                    'question_id' => $old_question,
                    'degree' => $request->question_degree ?? 1
                ];
            }
        }
        
        if ($request->filled('new_questions')) {
            $course_id = $quiz->course_detail_id;
            
            foreach ($request->new_questions as $new_question) {
                $new_question['course_id'] = $course_id;
                $question = QuestionsServices::addNewQuestion($new_question);    
                $questions[] = [
                    'question_id' => $question->id,
                    'degree' => $question_degree ?? 1
                ];
            }
        }
        return $questions;
    }

    private function deadlineCheck($quiz)
    {
        $start = Carbon::parse($quiz->date . ' ' . $quiz->start_time);
        $end = $start->copy()->addMinutes($quiz->duration);
        $now = now();

        // التحقق من توقيت الاختبار
        if ($now->lt($start) || $now->gt($end)) {
            throw new AccessDeniedHttpException('Quiz is not available at this time.');
        }
    }

    private function submitedBeforeCheck($quiz_id, $studentId)
    {
        // التحقق من التقديم المسبق
        $alreadySubmitted = QuizAnswer::where('quiz_id', $quiz_id)
            ->where('student_id', $studentId)
            ->exists();

        if ($alreadySubmitted) {
            throw new AccessDeniedHttpException('You have already submitted this quiz.');
        }
    }

}
