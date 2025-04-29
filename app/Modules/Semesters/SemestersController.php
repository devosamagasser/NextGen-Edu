<?php

namespace App\Modules\Semesters;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Modules\Courses\Resources\SemesterResource;


class SemestersController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $semesters = Semester::get();
        return ApiResponse::success(SemesterResource::collection($semesters));
    }

}
