<?php

namespace App\Modules\CourseMaterials;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'department_id',
        'semester_id',
        'title',
        'material_type',
        'material_url',
        'description',
    ];
}
