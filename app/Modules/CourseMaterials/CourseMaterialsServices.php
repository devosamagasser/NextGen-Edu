<?php

namespace App\Modules\CourseMaterials;

use App\Facades\FileHandler;
use App\Services\Service;
use App\Models\CourseDetail;
use App\Modules\Courses\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Exception\UnexpectedValueException;

class CourseMaterialsServices extends Service
{

    /**
     * Display the specified resource.
     */
    public function getCourseMaterialsById(string $id)
    {
        $user = auth()->user();

        $courseDetails = CourseDetail::findOrFail($id);
        if ($user->hasRole('Teacher')) {
            return CourseMaterial::where('course_id', $courseDetails->course_id)
                ->where('department_id', $courseDetails->department_id)
                ->where('semester_id', $courseDetails->semester_id)
                ->get();
        } elseif ($user->hasRole('Student')) {
            return CourseMaterial::where('course_id', $courseDetails->course_id)
                ->where('department_id', $user->students->department_id)
                ->where('semester_id', $user->students->semester_id)
                ->get();
        }

    }

    public function getMaterialById(string $id)
    {
        return CourseMaterial::findOrFail($id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addNewMaterial($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $courseDetails = CourseDetail::findOrfail($id);
            $materialPath = FileHandler::storeFile(
                $request->file('material'),
                'course_materials',
                $request->file('material')->getClientOriginalExtension() 
            );
            return CourseMaterial::create([
                'title' => $request->title,
                'course_id' => $courseDetails->course_id,
                'department_id' => $courseDetails->department_id,
                'semester_id' => $courseDetails->semester_id,
                'course_details_id' => $id,
                'material' => $materialPath,
                'week' => $request->week,
            ]);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateMaterialInfo($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $courseMaterial = CourseMaterial::findOrFail($id);
            if($request->hasFile('material'))
            $materialPath = FileHandler::updateFile(
                $request->file('material'),
                $courseMaterial->material,
                'course_materials',
                $request->file('material')->getClientOriginalExtension() 
            );
            return $courseMaterial->update([
                'title' => $request->title ?? $courseMaterial->title,
                'material' => $materialPath ?? $courseMaterial->material,
                'week' => $request->week ?? $courseMaterial->week,
            ]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteMaterial(string $id)
    {
        return CourseMaterial::findOrFail($id)->delete();
    }
}
