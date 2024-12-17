<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CourseStoreRequest;
use App\Http\Requests\Dashboard\CourseUpdateRequest;
use App\Services\Dashboard\CoursesServices;

class CoursesController extends Controller
{
    public function __construct(public CoursesServices $courseServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->courseServices->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseStoreRequest $request)
    {
        return $this->courseServices->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->courseServices->show( $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseUpdateRequest $request, string $id)
    {
        return $this->courseServices->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->courseServices->destroy($id);
    }
}
