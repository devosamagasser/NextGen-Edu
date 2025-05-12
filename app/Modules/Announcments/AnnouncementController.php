<?php

namespace App\Modules\Announcments;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Announcments\Validation\AnnouncementStoreRequest;
use App\Modules\Announcments\Validation\AnnouncementUpdateRequest;
use App\Modules\Announcments\AnnounecmentResource;

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
        return ApiResponse::success(AnnounecmentResource::collection($announcements)->resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnnouncementStoreRequest $request)
    {
        $this->announcemetsServices->addNewAnnouncement($request);
        return ApiResponse::message('Created successfully');
    }

    public function showMine()
    {
        $announcements = $this->announcemetsServices->myAnnouncements();
        return ApiResponse::success(AnnounecmentResource::collection($announcements)->resource);
    }

    public function show(string $id)
    {
        $announcement = $this->announcemetsServices->announcement($id);
        return ApiResponse::success(new AnnounecmentResource($announcement));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnnouncementUpdateRequest $request, string $id)
    {
        $announcement = $this->announcemetsServices->updateAnnouncementInfo($request, $id);
        return ($announcement) ? ApiResponse::message('Created successfully') : ApiResponse::message('No cahnge');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->announcemetsServices->deleteAnnouncement($id);
        return ApiResponse::deleted();
    }
}
