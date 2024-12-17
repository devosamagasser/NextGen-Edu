<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AdminStoreRequest;
use App\Http\Requests\Dashboard\AdminUpdateRequest;
use App\Services\Dashboard\AdminsServices;

class AdminsController extends Controller
{
    public function __construct(public AdminsServices $adminServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->adminServices->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminStoreRequest $request)
    {
        return $this->adminServices->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->adminServices->show( $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUpdateRequest $request, string $id)
    {
        return $this->adminServices->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->adminServices->destroy($id);
    }
}
