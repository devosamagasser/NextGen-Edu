<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\StudentsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StudentStoreRequest;
use App\Http\Requests\Dashboard\StudentUpdateRequest;
use App\Services\Dashboard\StudentsServices;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentsController extends Controller
{
    public function __construct(public StudentsServices $studentsServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->studentsServices->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentStoreRequest $request)
    {
        return $this->studentsServices->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->studentsServices->show( $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StudentUpdateRequest $request, string $id)
    {
        return $this->studentsServices->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->studentsServices->destroy($id);
    }

    public function export()
    {
        return $this->studentsServices->export();
    }

    public function import(Request $request)
    {
        return $this->studentsServices->import($request);
    }

}
