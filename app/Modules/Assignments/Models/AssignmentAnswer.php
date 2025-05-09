<?php

namespace App\Modules\Assignments\Models;

use App\Modules\Students\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssignmentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assignment_id',
        'file',
        'degree',
        'status'
    ];


    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function getFileUrlAttribute()
    {
        return Storage::disk('public')->url($this->file) ;
    }


}
