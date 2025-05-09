<?php

namespace App\Models;

use App\Modules\Students\Student;
use App\Modules\Teachers\Teacher;
use Laravel\Sanctum\HasApiTokens;
use App\Modules\Quizzes\Models\Quiz;
use App\Modules\Assignments\Models\Assignment;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeRole(Builder $builder,$user)
    {
        return $builder->whereType($user);
    }

    public function teachers()
    {
        return $this->hasOne(Teacher::class);
    }

    public function students()
    {
        return $this->hasOne(Student::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'teacher_id')->with('courseDetail.course','questions.answers');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'teacher_id')->with('courseDetail.course');
    }


    public function getAvatarUrlAttribute()
    {
        return Storage::disk('public')->url($this->avatar);
    }

}