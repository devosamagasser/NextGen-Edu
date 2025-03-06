<?php

namespace App\Modules\Buildings;

use App\Modules\Halls\Hall;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at','id'];

    public function halls()
    {
        return $this->hasMany(Hall::class);
    }
}
