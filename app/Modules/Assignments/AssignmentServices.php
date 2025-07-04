<?php

namespace App\Modules\Assignments;

use App\Services\Service;
use App\Facades\ApiResponse;
use App\Facades\FileHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Modules\Assignments\Models\Assignment;
use App\Modules\Assignments\Models\AssignmentAnswer;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AssignmentServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function getAllAssignments()
    {
        $user = request()->user();

        $assignments = Assignment::with('courseDetail', 'course', 'department', 'semester', 'teacher.user')
            ->orderBy('id', 'desc')
            ->filter()
            ->when($user->hasRole('Teacher'), function ($query) use ($user) {
                $query->whereHas('courseDetail.teachers', function ($q) use ($user) {
                    $q->where('course_teachers.teacher_id', $user->teachers->id);
                });
            })
            ->when($user->hasRole('Student'), function ($query) use ($user) {
                $query->whereHas('courseDetail', function ($q) use ($user) {
                    $q->where([
                        ['department_id', $user->students->department_id],
                        ['semester_id', $user->students->semester_id],
                    ]);
                });

            })->get();
        
        $this->statusHandler($assignments);
        
        return $assignments;
    }

    private function statusHandler($assignments)
    {
        $assignments->each(function ($assignment) {
            if (now()->greaterThanOrEqualTo($assignment->deadline) && $assignment->status !== 'finished') {
                $assignment->update(['status' => 'finished']);
            }
        });
    }

    /**
     * Display the specified resource.
     */
    public function getAssignmentById($id)
    {
        $user = request()->user();
        return Assignment::with([
            'courseDetail',
            'course',
            'department',
            'semester',
            'teacher.user',
            'answers' => function ($query) use($user){
                $query->when($user->type == 'Students', function ($q) use($user) {
                    $q->where('assignments_answers.student_id', $user->students->id);
                });
            },
        ])->findOrFail($id);
    }
    
    // public function getAssignmentAnswers($id)
    // {
    //     return Assignment::with('answers.student.user')->findOrFail($id);
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function addNewAssignment($request)
    {
        return DB::transaction(function () use($request) {
            $user = request()->user();
            $file = FileHandler::storeFile(
                $request->file('file'), 
                'assignments',
                $request->file->getClientOriginalExtension(),
            );

            $assignment = Assignment::create([
                'teacher_id' => $user->teachers->id,
                'course_detail_id' => $request->course_id,  
                'title' => $request->title,
                'file' => $file,
                'description' => $request->description,
                'total_degree' => $request->total_degree,
                'deadline' => $request->date . ' ' . $request->time,
                'status' => 'scheduled',
            ]);
            
            event(new \App\Events\AssignmentCreated($assignment));
            
            return $assignment;
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

            $data = [ 
                'course_detail_id' => $request->course_id,
                'title' => $request->title,
                'description' => $request->description,
                'total_degree' => $request->total_degree,
            ];

            if($request->filled('date') || $request->filled('time')){
                if($assignment->status !=  'scheduled'){
                    throw new AccessDeniedHttpException('you can not update this assignment\'s time because it is already finished');
                }
                $data['deadline'] = $request->date . ' ' . $request->time;
            }

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
        $studentId = $user->students->id;
        $assignment = Assignment::findOrFail($assignment_id);
    
        $this->checkAssignmentDeadline($assignment);
    
        $answer = AssignmentAnswer::where('student_id', $studentId)
            ->where('assignment_id', $assignment_id)
            ->first();
    
        if ($answer && $answer->status === 'corrected') {
            throw new AccessDeniedHttpException('The answer is corrected.');
        }
    
        $path = $this->handleFileUpload($file, $answer?->file);
    
        if ($answer) {
            $answer->update(['file' => $path]);
        } else {
            AssignmentAnswer::create([
                'student_id' => $studentId,
                'assignment_id' => $assignment_id,
                'file' => $path,
            ]);
        }
    
        return Storage::disk('public')->url($path);
    }
    

    public function assignDegree($assignmentId, $degree)
    {
        $answer = AssignmentAnswer::with('assignment')
        ->findOrFail($assignmentId);
        if ($answer->assignment->total_degree < $degree) {
            throw new HttpResponseException(
                ApiResponse::validationError(['degree' => 'Degree must be less than or equal to the maximum degree of the assignment.'])
            );
        }
        $answer->update([
            'degree' => $degree,
            'status' => 'corrected'
        ]);
        return true;
    }

    private function checkAssignmentDeadline($assignment)
    {
        if (now()->gt($assignment->deadline)) {
            throw new AccessDeniedHttpException('Deadline has ended.');
        }
    }
    
    private function handleFileUpload($file, $oldFile = null)
    {
        $folder = 'assignments/answers';
        $extension = $file->getClientOriginalExtension();
    
        return $oldFile
            ? FileHandler::updateFile($file, $oldFile, $folder, $extension)
            : FileHandler::storeFile($file, $folder, $extension);
    }
    





}
