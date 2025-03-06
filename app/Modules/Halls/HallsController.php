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
        $halls = $this->hallsServices->getAllHalls($building);
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
        return ApiResponse::updated($hall);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->hallsServices->deleteHall($id);
        return ApiResponse::deleted();
    }
}
