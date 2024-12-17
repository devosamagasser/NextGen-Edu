<?php

namespace App\Services\Dashboard;

use App\Models\Department;
use App\Services\Service;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class DepartmentsServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $departments = Department::withCount('teachers','courses','students')->get();
            return apiResponse(['data' => $departments]);
        } catch (Exception $e) {
            return apiResponse(null, 'Failed to get departments', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $department = Department::withCount('teachers','courses','students')->findOrFail($id);
            return apiResponse(['data' =>  $department]);
        } catch (ModelNotFoundException $e) {
            return apiResponse(null, 'Department not found', Response::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            return apiResponse(null, 'Failed to get department info..', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        try {
            $department = Department::create($request->all());
            return apiResponse(['data' => $department],'created successfully',Response::HTTP_CREATED);
        }catch (Exception $e){
            return apiResponse(null, 'Failed to create a new department', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        try {
            $department = Department::findOrFail($id);

            $data = $this->updatedDataFormated($request);

            $department->fill($data);
            if ($department->isDirty()) {
                $department->save();
                return apiResponse(['data' => $department], 'Updated successfully');
            }

            return apiResponse(null, 'No changes made');

        } catch (ModelNotFoundException $e) {
            return apiResponse(null, 'Department not found', Response::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            return apiResponse(null, 'Failed to update department info..', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $department = Department::findOrFail($id);

            $department->delete();

            return apiResponse(null, 'Deleted successfully');

        } catch (ModelNotFoundException $e) {
            return apiResponse(null, 'Department not found', Response::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            return apiResponse(null, 'Failed to delete department', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
