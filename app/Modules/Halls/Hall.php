<?php

namespace App\Modules\Halls;

use App\Modules\Buildings\Building;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

}
