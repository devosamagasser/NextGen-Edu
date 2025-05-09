<?php

namespace App\Modules\Quizzes;

use Carbon\Carbon;
use App\Services\Service;
use App\Models\CourseDetail;
use Illuminate\Support\Facades\DB;
use App\Modules\Quizzes\Models\Quiz;
use App\Modules\Quizzes\Models\QuizAnswer;
use App\Modules\Questions\QuestionsServices;
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
                $query->whereHas('courseDetails', function ($q) use ($user) {
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
            $questions = $this->questionHandler($request,$quiz);
            $quiz->questions()->attach($questions);

            return $quiz;
        });
    }
    

    /**
     * Update the specified resource in storage.
     */
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
        $quiz = Quiz::with('questions')->findOrFail($quiz_id);
        
        $start = Carbon::parse($quiz->date . ' ' . $quiz->start_time);
        $end = $start->copy()->addMinutes($quiz->duration);
        $now = now();
        
        if ($now->lt($start) || $now->gt($end)) {
            throw new AccessDeniedHttpException('Quiz is not available at this time.');
        }
        
        // تحقق إذا تم التسليم مسبقًا
        $alreadySubmitted = QuizAnswer::where('quiz_id', $quiz_id)
            ->where('student_id', $user->id)
            ->exists();
            
            if ($alreadySubmitted) {
            throw new AccessDeniedHttpException('You have already submitted this quiz.');
        }
    
        foreach ($questions as $data) {
            QuizAnswer::create([
                'quiz_id' => $quiz_id,
                'question_id' => $data['question'],
                'answer_id' => $data['answer'],
                'student_id' => $user->students->id,
            ]);
        }
    
        return true;
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

}
