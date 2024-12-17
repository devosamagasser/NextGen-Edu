<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\HallStoreRequest;
use App\Http\Requests\Dashboard\HallUpdateRequest;
use App\Services\Dashboard\HallsServices;

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
        return $this->hallsServices->index($building);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HallStoreRequest $request)
    {
        return $this->hallsServices->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->hallsServices->show( $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HallUpdateRequest $request, string $id)
    {
        return $this->hallsServices->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->hallsServices->destroy($id);
    }
}
