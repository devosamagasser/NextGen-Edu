<?php

namespace App\Modules\CourseMaterials;

use App\Facades\FileHandler;
use App\Services\Service;
use App\Models\CourseDetail;
use Illuminate\Support\Facades\DB;

class CourseMaterialsServices extends Service
{

    /**
     * Display the specified resource.
     */
    public function getCourseMaterialsById(string $id)
    {
        return CourseMaterial::where('course_detail_id', $id)->filter()->get();
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
            $data = [];
            foreach ($request->file('material') as $file) {
                $materialPath = FileHandler::storeFile(
                    $file,
                    'course_materials',
                    $file->getClientOriginalExtension() 
                );
                $data[] = [                
                    'title' => $request->title,
                    'course_detail_id' => $id,
                    'material' => $materialPath,
                    'week' => $request->week,
                    'type' => $request->type
                ];
            }
            event(new \App\Events\MaterialCreated($id));
            return  CourseMaterial::insert($data);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateMaterialInfo($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $courseMaterial = CourseMaterial::findOrFail($id);
            $materialPath = $courseMaterial->material; // Default to existing path
            if ($request->hasFile('material')) {
                $materialPath = FileHandler::updateFile(
                    $request->file('material'),
                    $courseMaterial->material,
                    'course_materials',
                    $request->file('material')->getClientOriginalExtension()
                );
            }
            return $courseMaterial->update([
                'title' => $request->title ?? $courseMaterial->title,
                'material' => $materialPath,
                'week' => $request->week ?? $courseMaterial->week,
                'type' => $request->type ?? $courseMaterial->type,
            ]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteMaterial(string $id)
    {
        $material = CourseMaterial::findOrFail($id);
        if ($material->material) {
            FileHandler::deleteFile($material->material);
        }
        return $material->delete();
         
    }
}
