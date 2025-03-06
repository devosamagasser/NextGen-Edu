<?php

namespace App\Modules\Table;

use App\Facades\ApiResponse;
use App\Modules\Table\Resources\TableResource;
use App\Http\Controllers\Controller;
use App\Modules\Table\Validation\TableStoreRequest;
use App\Modules\Table\Validation\TableUpdateRequest;

class TableController extends Controller
{
    public function __construct(public TableServices $tableServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $table = $this->tableServices->getTable();
        return ApiResponse::success(TableResource::collection($table));
    }

    public function studentTable()
    {
        $table = $this->tableServices->getStudentTable();
        return ApiResponse::success(TableResource::collection($table));
    }

    public function teacherTable()
    {
        $table = $this->tableServices->getTeacherTable();
        return ApiResponse::success(TableResource::collection($table));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TableStoreRequest $request)
    {
        $session = $this->tableServices->addNewSession($request);
        return ApiResponse::created($session);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TableUpdateRequest $request, string $id)
    {
        $session = $this->tableServices->updateSessionInfo($request, $id);
        if($session) {
            $session->save();
            return ApiResponse::updated($session);
        }
        return ApiResponse::message('no changes');
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     $teacher = $this->tableServices->deleteTeacher($id);
    //     return ApiResponse::deleted($teacher);
    // }
}
