<?php

namespace App\Modules\Assignments;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Assignments\Validation\SubmitAnswerRequest;
use App\Modules\Assignments\Validation\AssignmentStoreRequest;
use App\Modules\Assignments\Validation\AssignmentUpdateRequest;
use App\Modules\Assignments\Validation\AssignAnswerDegreeRequest;

class AssignmentController extends Controller
{
    public function __construct(public AssignmentServices $assignmentServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = $this->assignmentServices->getAllAssignments();
        return ApiResponse::success(AssignmentResource::collection($assignments));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssignmentStoreRequest $request)
    {
        $this->assignmentServices->addNewAssignment($request);
        return ApiResponse::message('created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $assignment = $this->assignmentServices->getAssignmentById($id);
        return ApiResponse::success(new AssignmentResource($assignment));
    }

    // public function showAnswers(string $id)
    // {
    //     $answers = $this->assignmentServices->getAssignmentAnswers($id);
    //     return ApiResponse::success(new AssignmentResource($answers));
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssignmentUpdateRequest $request, string $id)
    {
        $assignment = $this->assignmentServices->updateAssignmentInfo($request, $id);
        return ApiResponse::updated(new AssignmentResource($assignment));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->assignmentServices->deleteAssignment($id);
        return ApiResponse::deleted();
    }

    
    public function submit(SubmitAnswerRequest $request, string $id)
    {
    
        $path = $this->assignmentServices->submitAssignmentAnswer($id, $request->file);
        return ApiResponse::success([
            'answer'=> $path
        ],'submitted successfully');
    }
    
    public function assignDegree(AssignAnswerDegreeRequest $request, string $assignmentId)
    {
        $this->assignmentServices->assignDegree($assignmentId, $request->degree);
        return ApiResponse::message('successfully');
    }
    
    

}
