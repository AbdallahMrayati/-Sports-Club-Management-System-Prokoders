<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends BaseController
{
    public function index()
    {
        $facilities = Facility::with('sports')->get();
        return $this->sendResponse(FacilityResource::collection($facilities), 'Facilities retrieved successfully.');
    }

    public function store(StoreFacilityRequest $request)
    {
        $facility = Facility::create($request->validated());
        return $this->sendResponse(new FacilityResource($facility), 'Facility Added successfully.');
    }

    public function update(UpdateFacilityRequest $request, Facility $facility)
    {
        $facility->update($request->validated());
        return $this->sendResponse(new FacilityResource($facility), 'Facility Updated successfully.');
    }

    public function destroy(Facility $facility)
    {
        $facility->delete();
        return $this->sendResponse([], 'Facility Deleted successfully.');
    }
}