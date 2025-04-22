<?php

namespace App\Modules\Teachers;

use App\Models\User;
use App\Services\Service;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeachersServices extends Service
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
    public function addNewTeacher($request)
    {
        $teacher = null ;
        DB::transaction(function () use($request, &$teacher){
            $code = $this->generateCode();
            $email = "$code@zu.edu.eg";
            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'password' => Hash::make($email),
                'type' => "Teacher"
            ]);
            $user->assignRole('Teacher');

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'uni_code' => $code,
                'department_id' => $request->department_id,
                'description' => $request->description,
            ]);

        });

        return $teacher;
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

    public function mySemesters()
    {
        return request()->user()->teachers->semesters;
    }

    public function myDepartments()
    {
        return request()->user()->teachers->departments;
    }
}
