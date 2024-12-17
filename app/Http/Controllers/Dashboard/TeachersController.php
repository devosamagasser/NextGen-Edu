<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AdminUpdateRequest;
use App\Http\Requests\Dashboard\TeacherStoreRequest;
use App\Http\Requests\Dashboard\TeacherUpdateRequest;
use App\Models\User;
use App\Services\Dashboard\TeachersServices;

class TeachersController extends Controller
{
    public function __construct(public TeachersServices $teachersServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->teachersServices->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeacherStoreRequest $request)
    {
        return $this->teachersServices->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->teachersServices->show( $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeacherUpdateRequest $request, string $id)
    {
        return $this->teachersServices->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->teachersServices->destroy($id);
    }
}
