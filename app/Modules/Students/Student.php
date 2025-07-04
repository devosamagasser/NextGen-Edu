<?php

namespace App\Modules\Students;

use App\Models\Semester;
use App\Models\User;
use App\Modules\Departments\Department;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    
    public function scopeFilter(Builder $builder, $filterBy)
    {
        $builder->when($filterBy['department'] ?? null,function ($builder,$value){
            $builder->whereHas('department',function ($query) use($value){
                $query->where('department_id',$value);
            });
        });
        $builder->when($filterBy['semester'] ?? null,function ($builder,$value){
            $builder->whereHas('semester',function ($query) use($value){
                $query->where('semester_id',$value);
            });
        });
        $builder->when($filterBy['name'] ?? null,function ($builder,$value){
            $builder->whereHas('user',function ($query) use($value){
                $query->where('name','like',"%$value%");
            });
        });
    }

}
