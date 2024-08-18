<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends BaseController
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
    public function store(StoreMemberRequest $request)
    {
        DB::beginTransaction();

        try {
            // Create member
            $member = Member::create($request->only(['name', 'phone_number', 'email', 'balance']));

            // Attach sports to the member
            if ($request->has('sport_ids')) {
                $member->sports()->sync($request->input('sport_ids'));
            }

            // Attach subscriptions with start_date and end_date based on type
            if ($request->has('subscription_data')) {
                $subscriptionData = $request->input('subscription_data');

                foreach ($subscriptionData as $data) {
                    $subscriptionType = $data['type'];  // 'month', '3months', '6months', 'year'
                    $startDate = now();  // Start date is the current date
                    $endDate = $this->calculateEndDate($startDate, $subscriptionType);

                    $member->subscriptions()->attach($data['id'], [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'suspension_reason' => $data['suspension_reason'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return $this->sendResponse(new MemberResource($member->load(['sports', 'subscriptions'])), 'Member created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function calculateEndDate($startDate, $subscriptionType)
    {
        $startDate = \Carbon\Carbon::parse($startDate);
        switch ($subscriptionType) {
            case 'month':
                return $startDate->addMonth()->endOfMonth()->format('Y-m-d');
            case '3months':
                return $startDate->addMonths(3)->endOfMonth()->format('Y-m-d');
            case '6months':
                return $startDate->addMonths(6)->endOfMonth()->format('Y-m-d');
            case 'year':
                return $startDate->addYear()->endOfYear()->format('Y-m-d');
            default:
                throw new \InvalidArgumentException('Invalid subscription type');
        }
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
    public function update(UpdateMemberRequest $request, Member $member)
    {
        DB::beginTransaction();

        try {
            // Update member's basic information
            $member->update($request->only(['name', 'phone_number', 'email', 'balance']));

            // Update or sync sports
            if ($request->has('sport_ids')) {
                $member->sports()->sync($request->input('sport_ids'));
            }

            // Update or sync subscriptions with pivot data
            if ($request->has('subscription_data')) {
                $subscriptionData = $request->input('subscription_data');

                foreach ($subscriptionData as $data) {
                    $subscriptionType = $data['type'];  // 'month', '3months', '6months', 'year'
                    $startDate = $data['start_date'] ?? now();  // Use provided start date or current date
                    $endDate = $this->calculateEndDate($startDate, $subscriptionType);

                    // Update existing pivot with new start and end dates, suspension reason
                    $member->subscriptions()->syncWithoutDetaching([$data['id'] => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'suspension_reason' => $data['suspension_reason'] ?? null,
                    ]]);
                }
            }

            DB::commit();
            return $this->sendResponse(new MemberResource($member->load(['sports', 'subscriptions'])), 'Member updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}