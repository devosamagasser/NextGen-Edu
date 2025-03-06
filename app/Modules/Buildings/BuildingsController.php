<?php

namespace App\Modules\Buildings;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Buildings\Validation\BuildingStoreRequest;
use App\Modules\Buildings\Validation\BuildingUpdateRequest;

class BuildingsController extends Controller
{
    public function __construct(public BuildingsServices $buildingsServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $buildings =  $this->buildingsServices->getAllBuildings();
        return ApiResponse::success($buildings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BuildingStoreRequest $request)
    {
        $building = $this->buildingsServices->addNewBuilding($request);
        return ApiResponse::created($building);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $building = $this->buildingsServices->getBuildingById($id);
        return ApiResponse::sucess($building);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BuildingUpdateRequest $request, string $id)
    {
        $building = $this->buildingsServices->updateBuildingInfo($request, $id);
        return ApiResponse::updated($building);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->buildingsServices->deleteBuilding($id);
        return ApiResponse::deleted();
    }
}
