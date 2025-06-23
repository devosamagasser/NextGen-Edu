<?php

namespace App\Modules\Departments;

use App\Facades\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Modules\Departments\Validation\DepartmentStoreRequest;
use App\Modules\Departments\Validation\DepartmentUpdateRequest;

class DepartmentsController extends Controller
{
    public function __construct(public DepartmentsServices $departmentServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = $this->departmentServices->getAllDepartments();
        return ApiResponse::success(DepartmentResource::collection($departments));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DepartmentStoreRequest $request)
    {
        $department = $this->departmentServices->addNewDepartment($request);
        return ApiResponse::created($department);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $department = $this->departmentServices->getDapartmentById( $id);
        return ApiResponse::success(new DepartmentResource($department));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DepartmentUPdateRequest $request, string $id)
    {
        $department = $this->departmentServices->updateDepartmentInfo($request, $id);
        if ($department) {
            return ApiResponse::updated($department);
        }
        return ApiResponse::message('no change');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->departmentServices->deleteDepartment($id);
        return ApiResponse::deleted();
    }

    public function import(Request $request)
    {
        $this->departmentServices->import($request);
        return ApiResponse::success('successfully imported');
    }

}
