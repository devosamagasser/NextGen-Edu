<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Services\Service;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminsServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $admins = User::role('Admin')->simplePaginate(10);
            return apiResponse(['data' => $admins]);
        } catch (Exception $e) {
            return apiResponse(null, 'Failed to get admins', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $admin = User::role('Admin')->findOrFail($id);
            return apiResponse(['data' => $admin]);
        } catch (ModelNotFoundException $e) {
            return apiResponse(null, 'Admin not found', Response::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            return apiResponse(null, 'Failed to get admin info..', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        try {
            $user = null ;

            DB::transaction(function () use($request, &$user){
                 $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'type' => 'Admin'
                ]);

                $user->assignRole('Admin');
            });

            return apiResponse(
                ['data' => $user],
                'created successfully',
                Response::HTTP_CREATED
            );

        }catch (Exception $e){
            return apiResponse(
                null,
                'Failed to create admin',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        try {
            $admin = User::role('Admin')->findOrFail($id);

            $data = $this->updatedDataFormated($request);

            $admin->fill($data);
            if ($admin->isDirty()) {
                $admin->save();
                return apiResponse(['data' => $admin], 'Updated successfully');
            }

            return apiResponse(null, 'No changes made');

        } catch (ModelNotFoundException $e) {
            return apiResponse(null, 'Admin not found', Response::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            return apiResponse(null, 'Failed to update admin info..', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $admin = User::role('Admin')->findOrFail($id);

            $admin->delete();

            return apiResponse(null, 'Deleted successfully');

        } catch (ModelNotFoundException $e) {
            return apiResponse(null, 'Admin not found', Response::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            return apiResponse(null, 'Failed to delete admin', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
