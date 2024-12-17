<?php

namespace App\Services\Dashboard;

use App\Http\Resources\HallResource;
use App\Models\Building;
use App\Models\Hall;
use App\Services\Service;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class HallsServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function index($building)
    {
        try {
            Building::findOrFail($building);
            $halls = Hall::with('building')->where('building_id',$building)->get();
            return apiResponse(['data' => HallResource::collection($halls)]);
        } catch (ModelNotFoundException $e) {
            return apiResponse(null, 'Building not found', Response::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            return apiResponse(null, 'Failed to get halls', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $hall = Hall::with('building')->findOrFail($id);
            return apiResponse(['data' =>  new HallResource($hall)]);
        } catch (ModelNotFoundException $e) {
            return apiResponse(null, 'Hall not found', Response::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            return apiResponse(null, 'Failed to get hall info..', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        try {
            $hall = Hall::create($request->all());
            return apiResponse(['data' => $hall],'created successfully',Response::HTTP_CREATED);
        }catch (Exception $e){
            return apiResponse(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        try {
            $hall = Hall::findOrFail($id);
            $data = $this->updatedDataFormated($request);

            $hall->fill($data);
            if ($hall->isDirty()) {
                $hall->save();
                return apiResponse(['data' => $hall], 'Updated successfully');
            }

            return apiResponse(null, 'No changes made');

        } catch (ModelNotFoundException $e) {
            return apiResponse(null, 'Hall not found', Response::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            return apiResponse(null, 'Failed to update hall info..', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $hall = Hall::findOrFail($id);

            $hall->delete();

            return apiResponse(null, 'Deleted successfully');

        } catch (ModelNotFoundException $e) {
            return apiResponse(null, 'Hall not found', Response::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            return apiResponse(null, 'Failed to delete hall', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
