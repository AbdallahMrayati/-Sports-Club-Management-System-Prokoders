<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::all();
        return $this->sendResponse(RoomResource::collection($rooms), 'Rooms retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        $room = Room::create($request->validated());
        return $this->sendResponse(new RoomResource($room), 'Rooms Added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, Room $room)
    {
        $room->update($request->validated());
        return $this->sendResponse(new RoomResource($room), 'Room Updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $room->delete();
        return $this->sendResponse([], 'Rooms Deleted successfully.');
    }
}