<?php

namespace App\Modules\Halls;

use App\Facades\ApiResponse;
use App\Modules\Buildings\Building;
use App\Services\Service;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use function App\Services\Dashboard\apiResponse;

class HallsServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function getAllHallsByBuilding($building)
    {
        return Hall::with('building')->where('building_id',$building)->get();
    }
    
    public function getAllHalls()
    {
        return Hall::with('building')->get();
    }
    /**
     * Display the specified resource.
     */
    public function getHallById(string $id)
    {
        return Hall::with('building')->findOrFail($id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addNewHall($request)
    {
        return Hall::create($request->all());
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateHallInfo($request, $id)
    {
        $hall = Hall::findOrFail($id);
        $data = $this->updatedDataFormated($request);

        $hall->fill($data);
        if ($hall->isDirty()) {
            $hall->save();
            return $hall;
        }

        return false;

    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteHall($id)
    {
        Hall::findOrFail($id)->delete();
    }

    public function enterHall($hall_id)
    {
        $hall = Hall::findOrFail($hall_id);
        $hall->increment('audience');
        $hall->save();
    }

    public function exitHall($hall_id)
    {
        $hall = Hall::findOrFail($hall_id);
        if ($hall->audience > 0) {
            $hall->decrement('audience');
            $hall->save();
        }
    }
}
