<?php

namespace App\Modules\Halls;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Halls\Validation\HallStoreRequest;
use App\Modules\Halls\Validation\HallUpdateRequest;

class HallsController extends Controller
{
    public function __construct(public HallsServices $hallsServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index($building)
    {
        $halls = $this->hallsServices->getAllHallsByBuilding($building);
        return ApiResponse::success(HallResource::collection($halls));
    }
    
    public function all()
    {
        $halls = $this->hallsServices->getAllHalls();
        return ApiResponse::success(HallResource::collection($halls));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(HallStoreRequest $request)
    {
        $hall = $this->hallsServices->addNewHall($request);
        return ApiResponse::created($hall);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hall = $this->hallsServices->getHallById( $id);
        return ApiResponse::success(new HallResource($hall));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HallUpdateRequest $request, string $id)
    {
        $hall = $this->hallsServices->updateHallInfo($request, $id);
        if($hall){
            return ApiResponse::updated($hall);
        }
        return ApiResponse::message('No changes were made to the hall.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->hallsServices->deleteHall($id);
        return ApiResponse::deleted();
    }

    public function enter($hall_id)
    {
        $this->hallsServices->enterHall($hall_id);
        return ApiResponse::success(['message' => 'You have entered the hall.']);
    }

    public function exit($hall_id)
    {
        $this->hallsServices->exitHall($hall_id);
        return ApiResponse::success(['message' => 'You have exited the hall.']);
    }
    
}
