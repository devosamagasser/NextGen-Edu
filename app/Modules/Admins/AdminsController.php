<?php

namespace App\Modules\Admins;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Admins\Validation\AdminsStoreRequest;
use App\Modules\Admins\Validation\AdminsUpdateRequest;

class AdminsController extends Controller
{
    public function __construct(public AdminsServices $adminServices)
    {
    }

    public function index()
    {
        $admins = $this->adminServices->getAllAdmins();
        return ApiResponse::success($admins);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminsStoreRequest $request)
    {
        $admin = $this->adminServices->addNewAdmin($request);
        return ApiResponse::created($admin);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = $this->adminServices->getAdminById($id);
        return ApiResponse::success($admin);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminsUpdateRequest $request, string $id)
    {
        $newAdmin =  $this->adminServices->updateBuildingInfo($request, $id);
        if ($newAdmin) {
            return ApiResponse::updated($newAdmin);
        }
        return ApiResponse::message('No changes made');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->adminServices->deleteAdmin($id);
        return ApiResponse::deleted();
    }
}
