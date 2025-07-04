<?php

namespace App\Modules\CourseMaterials;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\CourseMaterials\MaterialsResource;
use App\Modules\CourseMaterials\Validation\MaterialStoreRequest;
use App\Modules\CourseMaterials\Validation\MaterialUpdateRequest;

class CourseMaterialsController extends Controller
{
    public function __construct(public CourseMaterialsServices $courseMaterialsServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $courses = $this->courseMaterialsServices->getCourseMaterialsById($id);
        return ApiResponse::success(MaterialsResource::collection($courses));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MaterialStoreRequest $request, $id)
    {
        $this->courseMaterialsServices->addNewMaterial($request, $id);
        return ApiResponse::message('created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $course = $this->courseMaterialsServices->getMaterialById($id);
        return ApiResponse::success(new MaterialsResource($course));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MaterialUpdateRequest $request, string $id)
    {
        $course = $this->courseMaterialsServices->updateMaterialInfo($request, $id);
        return ApiResponse::updated($course);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->courseMaterialsServices->deleteMaterial($id);
        return ApiResponse::deleted();
    }
}
