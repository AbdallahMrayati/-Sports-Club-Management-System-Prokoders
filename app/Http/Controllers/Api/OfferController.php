<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreOfferRequest;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OfferController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offers = Offer::all();
        return $this->sendResponse(OfferResource::collection($offers), 'Offers retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOfferRequest $request)
    {
        // Validate the request data
        $validated = $request->validated();

        // Convert dates to Carbon instances if not already
        $validated['start_date'] = Carbon::parse($validated['start_date']);
        $validated['end_date'] = Carbon::parse($validated['end_date']);

        // Create a new offer instance with the validated data
        $offer = Offer::create($validated);

        // Return a successful response with the created offer
        return $this->sendResponse(new OfferResource($offer), 'Offer Added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Offer $offer)
    {
        return $this->sendResponse(new OfferResource($offer), 'Offer retrieved successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        $offer->delete();
        return $this->sendResponse([], 'Offers Deleted successfully.');
    }
}