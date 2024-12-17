<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\DepartmentStoreRequest;
use App\Http\Requests\Dashboard\DepartmentUpdateRequest;
use App\Services\Dashboard\DepartmentsServices;

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
        return $this->departmentServices->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DepartmentStoreRequest $request)
    {
        return $this->departmentServices->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->departmentServices->show( $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DepartmentUPdateRequest $request, string $id)
    {
        return $this->departmentServices->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->departmentServices->destroy($id);
    }
}
