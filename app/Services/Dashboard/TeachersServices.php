<?php

namespace App\Services\Dashboard;

use App\Facades\ApiResponse;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use App\Models\User;
use App\Services\Service;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeachersServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $teachers = Teacher::with('user','department')->filter(request()->query())->simplePaginate(10);
            return ApiResponse::success(TeacherResource::collection($teachers));
        } catch (Exception $e) {
            dd($e->getMessage());
            return ApiResponse::serverError();
        }

    }

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
        try {
            $teacher = Teacher::with('user','department')->findOrFail($id);
            return ApiResponse::success(new TeacherResource($teacher));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Teacher not found');
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

            return ApiResponse::created(new TeacherResource($teacher));

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
            $teacher = Teacher::with('user')->findOrFail($id);

            if($request->user()->hasRole('Super admin') && $request->filled('department_id'))
                $teacher->department_id = $request->department_id;

            if($request->filled('description'))
                $teacher->description = $request->description;

            if($request->filled('name'))
                $teacher->user->name = $request->name;

            if(!$teacher->isDirty() && !$teacher->user->isDirty())
                return ApiResponse::message('No changes made');

            elseif ($teacher->isDirty())
                $teacher->save();

            else
                $teacher->user->save();

                return ApiResponse::updated(new TeacherResource($teacher));

        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Teacher not found');

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
            $userId = Teacher::findOrfail($id)->user_id;
            User::findOrfail($userId)->delete();
            return ApiResponse::message('Deleted successfully');

        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Teacher not found');

        } catch (Exception $e) {
            return ApiResponse::serverError();
        }
    }


    private function generateCode()
    {
        $uniCode = "3081".Carbon::now()->year."000000";
        $serial = (Teacher::select('uni_code')->latest()->first()->uni_code ?? $uniCode) + 1;
        return str_pad($serial , 6, '0', STR_PAD_LEFT);
    }
}
