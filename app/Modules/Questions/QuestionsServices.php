<?php

namespace App\Modules\Questions;

use Carbon\Carbon;
use App\Models\User;
use App\Services\Service;
use App\Modules\Teachers\Teacher;
use Illuminate\Support\Facades\DB;
use App\Modules\Questions\Models\Question;

class QuestionsServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function getAllTeachers()
    {
        return Teacher::with('user','department')
            ->filter(request()->query())
            ->simplePaginate(10);
    }

    /**
     * Display the specified resource.
     */
    public function getTeacherById($id)
    {
        return Teacher::with('user','department')->findOrFail($id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public static function addNewQuestion($request)
    {
        return DB::transaction(function () use($request) {

            $question =  Question::create([
                'course_id' => $request['course_id'],
                'question' => $request['question'], 
            ]);

            $data = [];
            foreach ($request['answers'] as $answer) {
                $data[] = [
                    'answer' => $answer['answer'],
                    'is_correct' => $answer['is_correct'],
                ];
            }

            $question->answers()->createMany($data);
            return $question;
        });

    }

    /**
     * Update the specified resource in storage.
     */
    public function updateTeacherInfo($request, $id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);

        if($request->user()->hasRole('Super admin') && $request->filled('department_id'))
            $teacher->department_id = $request->department_id;

        if($request->filled('description'))
            $teacher->description = $request->description;

        if($request->filled('name'))
            $teacher->user->name = $request->name;

        if(!$teacher->isDirty() && !$teacher->user->isDirty())
            return false;

        if ($teacher->isDirty())
            $teacher->save();

        if ($teacher->user->isDirty())
            $teacher->user->save();

        return $teacher;

    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteTeacher(string $id)
    {
        $userId = Teacher::findOrfail($id)->user_id;
        return User::findOrfail($userId)->delete();
    }


    private function generateCode()
    {
        $uniCode = "3081".Carbon::now()->year."000000";
        $serial = (Teacher::select('uni_code')->latest()->first()->uni_code ?? $uniCode) + 1;
        return str_pad($serial , 6, '0', STR_PAD_LEFT);
    }

    public function myCourses()
    {
        return request()->user()->teachers->courses;
    }
}
