<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Offer;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionRequest $request)
    {
        DB::beginTransaction();

        try {

            $subscription  = Subscription::create($request->validated());

            // Attach related models
            if ($request->has('sport_ids')) {
                $subscription->sports()->attach($request->input('sport_ids'));
            }

            if ($request->has('offer_ids')) {
                $offerIds = $request->input('offer_ids');
                $discountPercentage = $this->calculateTotalDiscount($offerIds);
                $subscription->price_after = $this->calculatePriceAfterDiscount($subscription->price, $discountPercentage);
                $subscription->offers()->attach($offerIds);
                $subscription->save();
            }

            DB::commit();
            return $this->sendResponse(new SubscriptionResource($subscription->load(['offers', 'sports', 'members'])), 'Subscription Added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    private function calculateTotalDiscount($offerIds)
    {
        $discountPercentage = 0;
        foreach ($offerIds as $offerId) {
            $offer = Offer::find($offerId);
            if ($offer) {
                $discountPercentage += $offer->discount_percentage;
            }
        }
        return $discountPercentage;
    }

    private function calculatePriceAfterDiscount($price, $discountPercentage)
    {
        return $price - ($price * ($discountPercentage / 100));
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription)
    {
        DB::beginTransaction();

        try {
            // Update subscription data
            $subscription->update($request->validated());

            if ($request->has('sport_ids')) {
                $subscription->sports()->sync($request->input('sport_ids'));
            }

            if ($request->has('offer_ids')) {
                $offerIds = $request->input('offer_ids');
                $discountPercentage = $this->calculateTotalDiscount($offerIds);
                $subscription->price_after = $this->calculatePriceAfterDiscount($subscription->price, $discountPercentage);
                $subscription->offers()->sync($offerIds); // Use sync for update
                $subscription->save();
            }

            DB::commit();
            return $this->sendResponse(new SubscriptionResource($subscription->load(['offers', 'sports', 'members'])), 'Subscription Updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // This method is shared between store and update



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        DB::beginTransaction();

        try {

            // Detach related models before deleting
            $subscription->sports()->detach();
            $subscription->offers()->detach();
            $subscription->members()->detach();

            // Delete the subscription
            $subscription->delete();

            DB::commit();
            return $this->sendResponse([], 'Subscription deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}