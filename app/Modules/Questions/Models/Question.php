<?php

namespace App\Modules\Questions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question', 
        'course_detail_id'
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

}
