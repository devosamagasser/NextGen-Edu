<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\BuildingStoreRequest;
use App\Http\Requests\Dashboard\BuildingUpdateRequest;
use App\Services\Dashboard\BuildingsServices;

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
        return $this->buildingsServices->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BuildingStoreRequest $request)
    {
        return $this->buildingsServices->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->buildingsServices->show( $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BuildingUpdateRequest $request, string $id)
    {
        return $this->buildingsServices->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->buildingsServices->destroy($id);
    }
}
