<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Modules\CourseMaterials\CourseMaterial;

class MaterialCreated
{
    use Dispatchable, SerializesModels;

    public $course_detail_id;

    public function __construct(string $course_detail_id)
    {
        $this->course_detail_id = $course_detail_id;
    }
} 