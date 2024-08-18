<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreSportRequest;
use App\Http\Requests\UpdateSportRequest;
use App\Http\Resources\SportResource;
use App\Models\Room;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SportController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sports = Sport::all();
        return $this->sendResponse(SportResource::collection($sports), 'Sports retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSportRequest $request)
    {
        DB::beginTransaction();

        try {
            $sport = Sport::create($request->validated());

            // Handle media uploads
            if ($request->hasFile('media')) {
                $mediaRecords = upload_media($request, 'media', $sport->id);

                // Attach media records to the sport
                if ($mediaRecords) {
                    $sport->media()->saveMany($mediaRecords);
                }
            }

            if ($request->has('day_ids')) {
                $sport->days()->attach($request->input('day_ids'));
            }


            if ($request->has('facility_ids')) {
                $sport->facilities()->attach($request->input('facility_ids'));
            }

            DB::commit();
            return $this->sendResponse(new SportResource($sport->load(['media', 'facilities', 'days'])), 'Sport Added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function show(Sport $sport)
    {
        return $this->sendResponse(new SportResource($sport->load(['media', 'rooms', 'facilities', 'days'])), 'Sport retrieved successfully.');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSportRequest $request, Sport $sport)
    {
        DB::beginTransaction();

        try {
            $sport->update($request->validated());

            // Handle media files
            if ($request->hasFile('media')) {
                // Call the helper function to upload media files
                $mediaRecords = upload_media($request, 'media', $sport->id);

                // Attach new media records to the sport
                if (!empty($mediaRecords)) {
                    $sport->media()->saveMany($mediaRecords);
                }
            }

            // Sync the day relationships
            if ($request->has('day_ids')) {
                $sport->days()->sync($request->input('day_ids'));
            }

            // Sync the facility relationships
            if ($request->has('facility_ids')) {
                $sport->facilities()->sync($request->input('facility_ids'));
            }

            DB::commit();
            return $this->sendResponse(new SportResource($sport->load(['media', 'facilities', 'days'])), 'Sport Updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sport $sport)
    {
        DB::beginTransaction();

        try {
            // Delete the associated media
            $sport->media()->delete();

            // Delete the associated relationships
            $sport->days()->detach();
            $sport->rooms()->detach();
            $sport->facilities()->detach();

            // Delete the Sport model
            $sport->delete();

            DB::commit();

            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}