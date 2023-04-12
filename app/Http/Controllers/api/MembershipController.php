<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all memberships from database
        $memberships = Membership::all();

        if (is_null($memberships)) {
            return response()->json([
                "success" => false,
                "message" => "Membership not found."
            ]);
        }

        // Return memberships as JSON response
        return response()->json([
            "success" => true,
            "message" => "Membership List",
            "data" => $memberships
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'amount' => 'required',
            'user_id' => 'required',
        ]);

        // Find the Membership model with the given User ID
        $membership = Membership::whereUserId($request->get('user_id'))->first();
        // If the Membership model is not null, return a failure response
        if ($membership) {
            return response()->json([
                "success" => false,
                "message" => "User already has a membership."
            ]);
        }
        // Create a new Membership model with request data
        $membership = new Membership([
            'amount' => $request->get('amount'),
            'status' => $request->get('status'),
            'user_id' => $request->get('user_id'),
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addMonths(1)->toDateString(),
        ]);

        // Save the new Membership model to the database
        $membership->save();

        // Return the new Membership as a JSON response
        return response()->json([
            "success" => true,
            "message" => "Membership created successfully.",
            "data" => $membership
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the Membership model with the given ID
        $membership = Membership::find($id);

        // If the Membership model is null, return a failure response
        if (is_null($membership)) {
            return response()->json([
                "success" => false,
                "message" => "Membership not found."
            ]);
        }

        // Return the Membership as a JSON response
        return response()->json([
            "success" => true,
            "message" => "Membership retrieved successfully.",
            "data" => $membership
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function disable(Request $request, string $id)
    {
        // Validate request data
        $request->validate([
            'status' => 'required',
        ]);

        // Find the Membership model with the given ID
        $membership = Membership::find($id);

        // Update the status of the Membership model with request data
        $membership->status = $request->get('status');
        $membership->save();

        // Return the updated Membership as a JSON response
        return response()->json([
            "success" => true,
            "message" => "Membership canceled successfully.",
            "data" => $membership
        ]);
    }

    /**
     * Update the membership status if the date is due.
     */
    public function cancelMembership(string $id)
    {
        // Find the Membership model with the given ID
        $membership = Membership::whereUserId($id)->first();


        if (Carbon::now()->gt($membership->end_date) || Carbon::now()->eq($membership->end_date)) {
            $membership->status = 'Canceled';
        }


        $membership->save();

        // Return the updated Membership as a JSON response
        return response()->json([
            "success" => true,
            "message" => "Membership canceled successfully.",
            "data" => $membership
        ]);
    }

    /**
     * Subtract credit from a user's membership.
     * @param  string  $id  The user ID.
     * @return \Illuminate\Http\JsonResponse The JSON response.
     */
    public function updateAmount(string $id)
    {
        // Find the membership for the specified user, or fail if not found.
        $membership = Membership::whereUserId($id)->firstOrFail();

        // Subtract 1 from the membership's amount.
        $currentAmount = $membership->amount - 1;

        // Update the membership's amount.
        $membership->amount = $currentAmount;
        $membership->save();

        // Return a JSON response indicating success and the updated membership.
        return response()->json([
            "success" => true,
            "message" => "Membership amount updated successfully.",
            "data" => $membership
        ]);
    }

    /**
     * Delete a membership.
     * @param  string  $id  The membership ID.
     * @return \Illuminate\Http\JsonResponse The JSON response.
     */
    public function destroy(string $id)
    {
        // Find the membership by ID.
        $membership = Membership::find($id);

        // Delete the membership.
        $membership->delete();

        // Return a JSON response indicating success and the deleted membership.
        return response()->json([
            "success" => true,
            "message" => "Membership deleted successfully.",
            "data" => $membership
        ]);
    }
}
