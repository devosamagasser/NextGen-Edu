<?php

namespace App\Services\Dashboard;

use App\Models\Building;
use App\Services\Service;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class BuildingsServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $buildings = Building::withCount('halls')->get();
            return apiResponse(['data' => $buildings]);
        } catch (Exception $e) {
            return apiResponse(null, 'Failed to get buildings', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $building = Building::withCount('halls')->findOrFail($id);
            return apiResponse(['data' =>  $building]);
        } catch (ModelNotFoundException $e) {
            return apiResponse(null, 'Building not found', Response::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            return apiResponse(null, 'Failed to get building info..', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        try {
            $building = Building::create($request->all());
            return apiResponse(['data' => $building],'created successfully',Response::HTTP_CREATED);
        }catch (Exception $e){
            return apiResponse(null, 'Failed to create a new building', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        try {
            $building = Building::findOrFail($id);
            $data = $this->updatedDataFormated($request);
            $building->fill($data);

            if ($building->isDirty()) {
                $building->save();
                return apiResponse(['data' => $building], 'Updated successfully');
            }

            return apiResponse(null, 'No changes made');

        } catch (ModelNotFoundException $e) {
            return apiResponse(null, 'Building not found', Response::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            return apiResponse(null, 'Failed to update building info..', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $building = Building::findOrFail($id);

            $building->delete();

            return apiResponse(null, 'Deleted successfully');

        } catch (ModelNotFoundException $e) {
            return apiResponse(null, 'Building not found', Response::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            return apiResponse(null, 'Failed to delete building', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
