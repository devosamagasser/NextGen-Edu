<?php

namespace App\Modules\Students;

use Carbon\Carbon;
use App\Models\User;
use App\Services\Service;
use App\Modules\Halls\Hall;
use App\Facades\ApiResponse;
use App\Models\CourseDetail;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use App\Modules\Courses\Course;
use App\Modules\Teachers\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StudentsServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function getAllStudents()
    {
        return Student::with('user','department','semester')
                ->filter(request()->query())
                ->get();
    }

    /**
     * Display the specified resource.
     */
    public function getStudentById($id)
    {
        return Student::with('user','department','semester')->findOrFail($id);
    }

    public function getStudentsByCourse($course_id)
    {
        $course = CourseDetail::findOrFail($course_id);
        $students = Student::with('user')
            ->where('department_id', $course->department_id)
            ->where('semester_id', $course->semester_id)
            ->get();
        return $students;
    }

    /**
     * Store a newly created resource in storage.
     */
  public function addNewStudent($request)
    {
        $code = $this->generateCode();
        $group = $this->generateGroupe($request->department_id, $request->semester_id);
        $email = $code . '@zu.edu.eg';
        $student = DB::transaction(function () use ($request, $code, $group, $email) {

            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'password' => Hash::make($email),
                'type' => "Student"
            ]);
            $user->assignRole('Student');

            return Student::create([
                'user_id' => $user->id,
                'uni_code' => $code,
                'department_id' => $request->department_id,
                'semester_id' => $request->semester_id,
                'nationality' => $request->nationality,
                'personal_id' => $request->personal_id,
                'group' => $group
            ]);
        });

        // إرسال الكود بعد نجاح الحفظ
        Http::withHeaders([
            "Content-Type" => "application/json",
            'Authorization' => 'Bearer 765|LcXERXtUwbmVHkOQ2ntDvzPhxz8LjMmVWOMPbUWZc0a149dc',
        ])->post('https://ngu-question-hub.azurewebsites.net/chat/add', [
            'userCode' => $student->uni_code,
        ]);

        return $student;
    }


    /**
     * Update the specified resource in storage.
     */
    public function updateStudentInfo($request, $id)
    {
        $student = Student::with('user')->findOrFail($id);
        $data = [];
        if($request->user()->hasRole('Super admin'))
            $data = $this->updatedDataFormated($request,$request->except('name'));

        if($request->filled('name')){
            $student->user->name = $request->name;
        }
        $student->fill($data);

        if (!$student->isDirty() && !$student->user->isDirty()){
            return false;
        }
        if ($student->isDirty()) {
            $student->save();
        }
        if ($student->user->isDirty()) {
            $student->user->save();
        }
        return $student;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteStudent(string $id)
    {
        $user = Student::with('user')->findOrFail($id)->user_id;
        User::findOrFail($user)->delete();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer kfxuzk1pQESIimcee9rivOXGttoHiC8IlXaBFxhc3Y',
        ])->send('DELETE', 'https://ngu-question-hub.azurewebsites.net/users/delete', [
            'json' => [
                'userId' => $user,
            ],
        ]);

        logger($response->status());
        logger($response->body());

    }

    public function export()
    {
        return Excel::download(new StudentsExport, 'students1.xlsx');
    }

    public function import($request)
    {
       try {
            Excel::import(new StudentsImport(), $request->file);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException($e->getMessage());
        }
    }

    public static function generateCode()
    {
        $uniCode = "2081".Carbon::now()->year."000000";
        $serial = (Student::select('uni_code')->latest()->first()->uni_code ?? $uniCode) + 1;
        while (Student::where('uni_code', $serial)->exists()) {
            $serial++;
        }
        return str_pad($serial , 6, '0', STR_PAD_LEFT);
    }
    

    public static function generateGroupe($department_id, $semester_id, $groupMax = 20 )
    {
        $query = Student::where('semester_id', $semester_id)
            ->where('department_id', $department_id);
        
        $maxGroup = $query->max('group') ?? 1;
        $group = $query->where('group', $maxGroup)->count() >= $groupMax ? $maxGroup + 1 : $maxGroup;

        return $group;
    }

    public function myCourses()
    {
        $semester_id = request()->user()->students->semester->id;
        $department_id = request()->user()->students->department->id;

        return Course::with('semesters','departments','courseDetails')
            ->whereHas('courseDetails',function($q)use($semester_id, $department_id){
                $q->where('semester_id', $semester_id);
                $q->where('department_id', $department_id);
            })->get();
    }

    public function attendance($hall_id, $student_id)
    {
        $hall = Hall::findOrFail($hall_id);
        $student = Student::where('uni_code',$student_id)->first();
        if(!$student)
        $student = Teacher::where('uni_code',$student_id)->first();
        // $attendance = Attendance::where('hall_id', $hall_id)
        //     ->where('student_id', $student_id)
        //     ->first();

        // if ($attendance) {
        //     return false; // Already marked attendance
        // }

        // $attendance = new Attendance();
        // $attendance->hall_id = $hall_id;
        // $attendance->student_id = $student_id;
        // $attendance->save();

        // $hall->increment('audience');
        // return true;
    }
}
