<?php

namespace App\Modules\Departments;

use App\Services\Service;
use App\Imports\DepartmentImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use function Laravel\Prompts\select;

class DepartmentsServices extends Service
{

    /**
     * Display a listing of the resource.
     */
    public function getAllDepartments()
    {
        return Department::withCount('teachers','courses','students')->get();
    }

    /**
     * Display the specified resource.
     */
    public function getDapartmentById(string $id)
    {
        return Department::withCount('teachers','courses','students')->findOrFail($id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addNewDepartment($request)
    {
        return Department::create($request->all());
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateDepartmentInfo($request, $id)
    {
        $department = Department::findOrFail($id);
        $data = $this->updatedDataFormated($request);
        
        $department->fill($data);
        if ($department->isDirty()) {
            $department->save();
            return $department;
        }
        return false;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteDepartment(string $id)
    {
        return Department::findOrFail($id)->delete();
    }

    public function import($request)
    {
       try {
            Excel::import(new DepartmentImport(), $request->file);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException($e->getMessage());
        }catch (\Exception $e) {
            throw new \Exception("Failed to import department: " . $e->getMessage());
        }
    }
}
