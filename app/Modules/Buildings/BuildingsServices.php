<?php

namespace App\Modules\Buildings;

use App\Services\Service;
use function App\Services\Dashboard\apiResponse;

class BuildingsServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function getAllBuildings()
    {
        return Building::withCount('halls')->get();
    }

    /**
     * Display the specified resource.
     */
    public function getBuildingById(string $id)
    {
        return Building::withCount('halls')->findOrFail($id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addNewBuilding($request)
    {
        return Building::create($request->all());
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateBuildingInfo($request, $id)
    {
        $building = Building::findOrFail($id);
        $data = $this->updatedDataFormated($request);
        $building->fill($data);
        if($building->isDirty()){
            $building->save();
            return $building;
        }
        return false;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteBuilding(string $id)
    {
        Building::findOrFail($id)->delete();
    }
}
