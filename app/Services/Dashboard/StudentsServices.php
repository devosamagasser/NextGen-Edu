<?php

namespace App\Services\Dashboard;

use App\Exports\StudentsExport;
use App\Facades\ApiResponse;
use App\Http\Resources\StudentResource;
use App\Imports\StudentsImport;
use App\Models\Student;
use App\Models\User;
use App\Services\Service;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class StudentsServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $students = Student::with('user','department','semester')->filter(request()->query())->simplePaginate(10);
            return ApiResponse::success(StudentResource::collection($students));
        } catch (Exception $e) {
            return ApiResponse::serverError();
        }

    }

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
        try {
            $student = Student::with('user','department','semester')->findOrFail($id);
            return ApiResponse::success(new StudentResource($student));

        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Student not found');

        } catch (Exception $e) {
            return ApiResponse::serverError();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        try {
            $student = null ;
            DB::transaction(function () use($request, &$student){
                $code = $this->generateCode();
                $email = $code.'@zu.edu.eg';
                $user = User::create([
                    'name' => $request->name,
                    'email' => $email,
                    'password' => Hash::make($email),
                    'type' => "Student"
                ]);
                $user->assignRole('Student');

                $student = Student::create([
                    'user_id' => $user->id,
                    'uni_code' => $code,
                    'department_id' => $request->department_id,
                    'semester_id' => $request->semester_id,
                    'nationality' => $request->nationality,
                    'personal_id' => $request->personal_id,
                    'group' => $request->group
                ]);

            });

            return ApiResponse::created(new StudentResource($student));

        }catch (Exception $e){
            return ApiResponse::serverError();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        try {
            $student = Student::with('user')->findOrFail($id);
            $data = [];
            if($request->user()->hasRole('Admin'))
                $data = $this->updatedDataFormated($request,$request->except('name'));

            if($request->filled('name'))
                $student->user->name = $request->name;

            $student->fill($data);
            if (!$student->isDirty() && !$student->user->isDirty())
                return ApiResponse::message('No changes made');

            else if ($student->isDirty())
                $student->save();

            else
                $student->user->save();

            return ApiResponse::updated(new StudentResource($student));

        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Student not found');

        } catch (Exception $e) {
            return ApiResponse::serverError();

        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = Student::with('user')->firstOrFail($id)->user_id;
            $student = User::findOrFail($user);
            $student->delete();

            return ApiResponse::message('Deleted successfully');

        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Student not found');

        } catch (Exception $e) {
            return ApiResponse::serverError();

        }
    }

    public function export()
    {
        try {
            return Excel::download(new StudentsExport, 'students.xlsx');

        } catch (Exception $e) {
            return ApiResponse::serverError();

        }
    }

    public function import($request)
    {
        try {
            Excel::import(new StudentsImport(), $request->file);
            return ApiResponse::message('uploaded successfully');
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        }catch (Exception $e){
            dump($e->getMessage());
            return ApiResponse::serverError();

        }
    }

    public static function generateCode()
    {
        $uniCode = "2081".Carbon::now()->year."000000";
        $serial = (Student::select('uni_code')->latest()->first()->uni_code ?? $uniCode) + 1;
        return str_pad($serial , 6, '0', STR_PAD_LEFT);
    }

}
