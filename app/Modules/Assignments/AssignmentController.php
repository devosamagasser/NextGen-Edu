<?php

namespace App\Modules\Assignments;

use App\Facades\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Assignments\Validation\AssignmentStoreRequest;
use App\Modules\Assignments\Validation\AssignmentUpdateRequest;

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
        $quiz = $this->assignmentServices->getAssignmentById($id);
        return ApiResponse::success(new AssignmentResource($quiz));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssignmentUpdateRequest $request, string $id)
    {
        $quiz = $this->assignmentServices->updateAssignmentInfo($request, $id);
        return ApiResponse::updated(new AssignmentResource($quiz));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->assignmentServices->deleteAssignment($id);
        return ApiResponse::deleted();
    }

    
    public function submit(Request $request, string $id)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx,jpg,png,jfif|max:2048',
        ]);
    
        $path = $this->assignmentServices->submitAssignmentAnswer($id, $request->file);
        return ApiResponse::success([
            'answer'=> $path
        ],'submitted successfully');
    }
    
    

}
