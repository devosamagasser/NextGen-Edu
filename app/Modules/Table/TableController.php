<?php

namespace App\Modules\Table;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Table\Models\Session;
use App\Modules\Table\Resources\TableResource;
use App\Modules\Table\Validation\TableUpdateRequest;
use App\Modules\Table\Validation\TableManuallyStoreRequest;
use App\Modules\Table\Validation\TableTeacherUpdateRequest;
use App\Modules\Table\Validation\TablePostponeSessionRequest;
use App\Modules\Table\Models\PostponedSession;

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
        $sessions = $this->tableServices->getTable();
        return ApiResponse::success($sessions);
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
    public function manually(TableManuallyStoreRequest $request, $department_id, $semester_id)
    {
        $session = $this->tableServices->addNewSession($request, $department_id, $semester_id);
        return ApiResponse::created($session);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TableUpdateRequest $request, $department_id, $semester_id)
    {
        $session = $this->tableServices->updateSessionInfo($request, $department_id, $semester_id);
        return ApiResponse::updated($session);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $department_id, string $semester_id,)
    {
        $this->tableServices->deleteTable($department_id, $semester_id);
        return ApiResponse::deleted(); 
    }

    public function postponeSessionByTeacher(TablePostponeSessionRequest $request, $session_id)
    {
        $session = Session::findOrFail($session_id);
        $session->status = 'postponed';
        $session->save();

        $data = $request->validated();
        $postponed = PostponedSession::updateOrCreate([
            'session_id' => $session->id,
        ],
        [
            'session_id' => $session->id,
            'date' => $data['date'] ?? $session->date,
            'day' => $data['day'] ?? $session->day,
            'from' => $data['from'] ?? $session->from,
            'to' => $data['to'] ?? $session->to,
            'hall_id' => $session->hall_id,
            'attendance' => $data['attendance'] ?? $session->attendance,
        ]);

        return response()->json([
            'message' => 'Session postponed successfully',
            'original_session' => $session,
            'postponed_session' => $postponed,
        ]);
    }
}
