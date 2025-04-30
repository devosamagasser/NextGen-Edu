<?php

namespace App\Modules\Assignments;

use App\Services\Service;
use App\Facades\FileHandler;
use App\Models\CourseDetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Modules\Assignments\Models\Assignment;
use App\Modules\Assignments\Models\AssignmentAnswer;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AssignmentServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function getAllAssignments()
    {
        $user = request()->user();
        if ($user->hasRole('Teacher')){
            $courseDetails = CourseDetail::where('teacher_id',$user->teachers->id);
            $assignments = Assignment::with('course', 'department', 'semester', 'teacher', 'teacher.user')
            ->whereIn('course_id',$courseDetails->pluck('course_id'))
            ->whereIn('department_id',$courseDetails->pluck('department_id'))
            ->whereIn('semester_id',$courseDetails->pluck('semester_id'))
            ->orderBy('id','desc')
            ->filter()
            ->get();
        }else if ($user->hasRole('Student')){
            $assignments = Assignment::with('course', 'department', 'semester', 'teacher', 'teacher.user')
            ->where('semester_id', $user->students->semester_id)
            ->where('department_id', $user->students->department_id)
            ->orderBy('id','desc')
            ->filter()
            ->get();
        }
        
        $assignments->each(function ($assignment) {
            if (now()->greaterThanOrEqualTo($assignment->deadline) && $assignment->status !== 'finished') {
                $assignment->update(['status' => 'finished']);
            }
        });
        
        return $assignments;
    }

    /**
     * Display the specified resource.
     */
    public function getAssignmentById($id)
    {
        return Assignment::with([
            'course',
            'department',
            'semester',
            'teacher',
            'teacher.user'
         ])->findOrFail($id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addNewAssignment($request)
    {
        return DB::transaction(function () use($request) {
            $user = request()->user();
            $count = count($request->file('file'));
            $CourseDetail = CourseDetail::findOrFail($request->course_id);
            $data = [];
            for ($i=0; $i < $count; $i++) { 
                $file = FileHandler::storeFile(
                    $request->file('file'), 
                    'assignments',
                    $request->file->getClientOriginalExtension(),
                );

                $data[] = [
                    'teacher_id' => $user->teachers->id,
                    'department_id' => $CourseDetail->department_id,
                    'semester_id' => $CourseDetail->semester_id,
                    'course_id' => $CourseDetail->course_id,  
                    'course_details_id' => $request->course_id,  
                    'title' => $request->title,
                    'file' => $file,
                    'description' => $request->description,
                    'total_degree' => $request->total_degree,
                    'deadline' => $request->date . ' ' . $request->time,
                    'status' => 'scheduled',
                ];

            }

            $assignment = Assignment::insert($data);
            
            return true;
        });
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function updateAssignmentInfo($request,string $id)
    {
        return DB::transaction(function () use($request,$id) {
            $user = request()->user();

            $assignment = Assignment::where('teacher_id',$user->teachers->id)->findOrFail($id);
            $CourseDetail = CourseDetail::findOrFail($request->course_id);

            $data = [
                'department_id' => $CourseDetail->department_id,
                'semester_id' => $CourseDetail->semester_id,
                'course_id' => $CourseDetail->course_id,    
                'course_details_id' => $request->course_id,
                'title' => $request->title,
                'description' => $request->description,
                'total_degree' => $request->total_degree,
                'date' => $request->date . ' ' . $request->time,
            ];

            if($request->hasFile('file'))
                $data['file'] = FileHandler::updateFile(
                    $request->file('file'), 
                    $assignment->file,
                    'assignments',
                    $request->file->getClientOriginalExtension(),
                );

            $assignment->update($data);

            return $assignment;
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteAssignment(string $id)
    {
        $user = request()->user();
        $assignment = Assignment::where('teacher_id',$user->teachers->id)->findOrFail($id);
        FileHandler::deleteFile($assignment->file);
        return $assignment->delete();
    }
    
    public function submitAssignmentAnswer($assignment_id, $file)
    {
        $user = request()->user();
        $assignments = Assignment::findOrFail($assignment_id);
        
        $start = $assignments->deadline;
        $now = now();
        
        if ($now->gt($start)) {
            throw new AccessDeniedHttpException('Deadine is end.');
        }

        $assignment = AssignmentAnswer::where('student_id',$user->students->id)
                        ->where('assignment_id',$assignment_id);

        if($assignment->where('status','corrected')->exists()){
            throw new AccessDeniedHttpException('The answer is corrected.');
        }

        if($assignment->where('status','submitted')->exists()){
            $assignment = $assignment->first();
            $path = FileHandler::updateFile(
                $file,
                $assignment->file,
                'assignments\answers',
                $file->getClientOriginalExtension(),
            );

            $assignment->update([
                'file' => $path,
            ]);
        }else{
            $path = FileHandler::storeFile(
                $file, 
                'assignments\answers',
                $file->getClientOriginalExtension(),
            );
            AssignmentAnswer::create([
                'student_id' => $user->students->id,
                'assignment_id' => $assignment_id,
                'file' => $path,
            ]);
        }

    
        return Config::get('filesystems.images_url') . $path;
    }




}
