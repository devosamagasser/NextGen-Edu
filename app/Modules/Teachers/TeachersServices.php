<?php

namespace App\Modules\Teachers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Semester;
use App\Services\Service;
use App\Exports\TeachersExport;
use App\Imports\TeachersImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TeachersServices extends Service
{
    /**
     * Display a listing of the resource.
     */
    public function getAllTeachers()
    {
        return Teacher::with('user', 'department')
            ->filter(request()->query())
            ->get();
    }

    /**
     * Display the specified resource.
     */
    public function getTeacherById($id)
    {
        return Teacher::with('user', 'department')->findOrFail($id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addNewTeacher($request)
    {
        $teacher = null;

        DB::transaction(function () use ($request, &$teacher) {
            $code = $this->generateCode();
            $email = "$code@zu.edu.eg";

            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'password' => Hash::make($email),
                'type' => "Teacher",
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

        if ($request->user()->hasRole('Super admin') && $request->filled('department_id')) {
            $teacher->department_id = $request->department_id;
        }

        if ($request->filled('description')) {
            $teacher->description = $request->description;
        }

        if ($request->filled('name')) {
            $teacher->user->name = $request->name;
        }

        if (!$teacher->isDirty() && !$teacher->user->isDirty()) {
            return false;
        }

        DB::transaction(function () use ($teacher) {
            if ($teacher->isDirty()) {
                $teacher->save();
            }

            if ($teacher->user->isDirty()) {
                $teacher->user->save();
            }
        });

        return $teacher;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteTeacher(string $id)
    {
        $teacher = Teacher::findOrFail($id);
        User::findOrFail($teacher->user_id)->delete();
        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer kfxuzk1pQESIimcee9rivOXGttoHiC8IlXaBFxhc3Y',
        ])->send('DELETE', 'https://ngu-question-hub.azurewebsites.net/users/delete', [
            'json' => [
                'userId' => $teacher,
            ],
        ]);
    }

    public function export()
    {
        return Excel::download(new TeachersExport, 'teacher.xlsx');
    }

    public function import($request)
    {
       try {
            Excel::import(new TeachersImport(), $request->file);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException($e->getMessage());
        }
    }

    public static function generateCode()
    {

        $uniCode = "3081".Carbon::now()->year."000000";
        $serial = (Teacher::select('uni_code')->latest()->first()->uni_code ?? $uniCode) + 1;
        while (Teacher::where('uni_code', $serial)->exists()) {
            $serial++;
        }
        return str_pad($serial , 6, '0', STR_PAD_LEFT);
    }

    public function myCourses()
    {
        return request()->user()->teachers
            ->courseDetails()
            ->with('course', 'department', 'semester')
            ->get();
    }
    

    public function mySemesters()
    {
        return request()->user()->teachers->courseDetails()->with('semester')->get()->pluck('semester')->unique('id');
    }

    public function myDepartments()
    {
        return request()->user()->teachers->courseDetails()->with('department')->get()->pluck('department')->unique('id');
    }
}
