<?php

namespace App\Models;

use App\Modules\Courses\Course;
use App\Modules\Students\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Semester extends Model
{
    use HasFactory;

    public function scopeTerm($query, $term = 1)
    {
        return $query->where('term', $term);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function courseDetails()
    {
        return $this->hasMany(CourseDetail::class);
    }
    
    public function courses()
    {
        return $this->hasManyThrough(Course::class, CourseDetail::class);
    }

    
}
