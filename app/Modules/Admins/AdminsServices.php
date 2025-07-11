<?php

namespace App\Modules\Admins;

use App\Models\User;
use App\Services\Service;
use App\Modules\Courses\Course;
use Illuminate\Support\Facades\DB;
use App\Modules\Buildings\Building;
use Illuminate\Support\Facades\Hash;
use App\Modules\Departments\Department;
use App\Modules\Halls\Hall;

class AdminsServices extends Service
{
    public string $role = 'Admin';

    /**
     * Display a listing of the resource.
     */
    public function getAllAdmins()
    {
        return User::role($this->role)->simplePaginate();
    }

    /**
     * Display the specified resource.
     */
    public function getAdminById(string $id)
    {
        return User::role($this->role)->findOrFail($id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addNewAdmin($request)
    {
        $user = null;
        DB::transaction(function () use ($request, &$user) {
             $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'type' => 'Admin'
            ]);
            $user->assignRole('Admin');
        });
        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateBuildingInfo($request, $id)
    {
        $admin = $this->getAdminById($id);
        $data = $this->updatedDataFormated($request);
        $admin->fill($data);
        if($admin->isDirty()){
            $admin->save();
            return $admin;
        }
        return false;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteAdmin(string $id)
    {
        $this->getAdminById($id)->delete();
        return true;
    }

    public function getStatistics()
    {
        $departmentsCount = Department::count();
        $coursesCount = Course::count();
        $buildingsCount = Building::count();
        $hallsCount = Hall::count();
        $studentsCount = User::role('Student')->count();
        $teachersCount = User::role('Teacher')->count();


        return [
            'students' => $studentsCount,
            'teachers' => $teachersCount,
            'departments' => $departmentsCount,
            'courses' => $coursesCount,
            'buildings' => $buildingsCount,
            'halls' => $hallsCount,
        ];
    }
}
