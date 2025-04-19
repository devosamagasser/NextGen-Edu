<?php

namespace App\Modules\Assignments\Models;

use App\Models\User;
use App\Models\CourseDetail;
use App\Modules\Teachers\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'course_detail_id',
        'title',
        'description',
        'file',
        'total_degree',
        'deadline',
        'status'
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];


    public function courseDetail()
    {
        return $this->belongsTo(CourseDetail::class)->with(['course','semester','department']);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }


    public function getFileUrlAttribute()
    {
        return config('filesystems.images_url') . $this->file ;
    }

    public function scopeFilter($query)
    {
        $query->when(request()->course,function($q, $value){
            $q->where('course_detail_id',$value);
        });
        $query->when(request()->status,function($q, $value){
            $q->where('status',$value);
        });
        $query->when(request()->from, function($q, $value){
            $fromDate = now()->subDays($value)->toDateString();
            $q->where('date', '>=', $fromDate);
        });
    }


    
}
