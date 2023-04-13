<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Membership;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Check in a user with a valid membership.
     *
     * @param string $id The ID of the user to check in.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating success or failure.
     */
    public function CheckIn(string $id)
    {
        // Create instances of the InvoiceController and MembershipController.
        $invoice = new InvoiceController();
        $membership = new MembershipController();

        // Find the user by ID.
        $user = User::find($id);

        // If the user doesn't have a membership, return an error.
        if (is_null($user->membership)) {
            return response()->json([
                "success" => false,
                "message" => "Membership not found."
            ]);
        }
        // If the user's membership has ended, return an error.
        else if((Carbon::now()->gt($user->membership->end_date) || Carbon::now()->eq($user->membership->end_date)) ){
            return response()->json([
                "success" => false,
                "message" => "Membership date is due.",
            ]);
        }
        // If the user's membership has been canceled, return an error.
        else if($user->membership->status == 'Canceled'){
            return response()->json([
                "success" => false,
                "message" => "Membership is canceled.",
            ]);
        }
        // If the user has no credits remaining, return an error.
        else if($user->membership->amount == 0){
            return response()->json([
                "success" => false,
                "message" => "No more credits",
            ]);
        }
        // Otherwise, create a new invoice and update the user's membership.
        else{
            $invoice->storeByInvoiceLine($id);
            $membership->updateAmount($id);
            return response()->json([
                "success" => true,
                "message" => "Check-in successful",
            ]);
        }
    }
}
