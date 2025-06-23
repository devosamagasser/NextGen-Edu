<?php

namespace App\Modules\Table\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostponedSession extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = "postponed_sessions";
}
