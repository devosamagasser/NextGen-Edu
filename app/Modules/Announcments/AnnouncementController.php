<?php

namespace App\Modules\Announcments;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Announcments\Validation\AnnouncementStoreRequest;
use App\Modules\Announcments\Validation\AnnouncementUpdateRequest;


class AnnouncementController extends Controller
{
    public function __construct(public AnnouncementsServices $announcemetsServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $announcements =  $this->announcemetsServices->getAllAnnouncements();
        return ApiResponse::success($announcements);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnnouncementStoreRequest $request)
    {
        $building = $this->announcemetsServices->addNewAnnouncement($request);
        return ApiResponse::created($building);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnnouncementUpdateRequest $request, string $id)
    {
        $announcement = $this->announcemetsServices->updateAnnouncementInfo($request, $id);
        return ($announcement) ? ApiResponse::updated($announcement) : ApiResponse::message('No cahnge');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->announcemetsServices->deleteBuilding($id);
        return ApiResponse::deleted();
    }
}
