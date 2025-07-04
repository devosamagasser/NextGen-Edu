<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Modules\Assignments\Models\Assignment;

class AssignmentCreated
{
    use Dispatchable, SerializesModels;

    public $assignment;

    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }
} 