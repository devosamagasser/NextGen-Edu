<?php

namespace App\Modules\Quizzes;

use Carbon\Carbon;
use App\Models\User;
use App\Services\Service;
use App\Modules\Teachers\Teacher;
use Illuminate\Support\Facades\DB;
use App\Modules\Quizzes\Models\Quiz;
use App\Modules\Questions\QuestionsServices;

class QuizzesServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function getAllQuizzes()
    {
        $user = request()->user();
        if ($user->hasRole('Teacher')){
            return $user->quizzes->load('courseDetail.course');
        }else if ($user->hasRole('Student')){
            return Quiz::with('courseDetail.course','teacher')
            ->whereHas('courseDetail',function($q) use($user){
                $q->where('semester_id', $user->students->semester_id)
                ->where('department_id', $user->students->department_id);
            })->orderBy('id','desc')
            ->filter()
            ->get();
        }
    }

    /**
     * Display the specified resource.
     */
    public function getQuizeById($id)
    {
        return Quiz::with('questions.answers','courseDetail.course','teacher')->findOrFail($id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addNewQuize($request)
    {
        return DB::transaction(function () use($request) {
            $user = request()->user();
            $quiz = Quiz::create([
                'teacher_id' => $user->id,
                'course_detail_id'  => $request->course_id,
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

            $quiz = Quiz::where('teacher_id',$user->id)->findOrFail($id);
            $quiz->update([
                'course_detail_id'  => $request->course_id,
                'title' => $request->title,
                'description' => $request->description,
                'total_degree' => $request->total_degree,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'duration' => $request->duration,
            ]);

            $quiz->questions()->detach();
            $questions = $this->questionHandler($request,$quiz);

            $quiz->questions()->attach($questions);
    
            return $quiz;
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteQuiz(string $id)
    {
        $user = request()->user();
        $quiz = Quiz::where('teacher_id',$user->id)->findOrFail($id);
        return $quiz->delete();
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
            $course_id = $quiz->courseDetail->course_id;

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
