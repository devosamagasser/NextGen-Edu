<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function semester()
    {
        $this->belongsTo(Semester::class);
    }

    public function department()
    {
        $this->belongsTo(Department::class);
    }

    public function Course()
    {
        $this->belongsTo(Course::class);
    }
    public function teacher()
    {
        $this->belongsTo(Teacher::class);
    }
    public function semesters()
    {
        $this->belongsTo(Semester::class);
    }
}
