<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\UserController;
use App\Models\Membership;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function testCheckInWithValidMembership()
    {
        // Create a user with a valid membership.
        $user = User::factory()->create();

        $membership = new Membership([
            'user_id' => $user->id,
            'amount' => 1,
            'status' => 'Active',
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addMonths(1)->toDateString(),
        ]);
        $membership->save();

        // Make the request to the CheckIn method.

        $response = $this->json('POST', route('user.checkin',$user->id));

        $response->assertJson([
            'success' => true,
            'message' => 'Check-in successful',
        ]);

        // Check that an invoice was created and the user's membership amount was updated.
        $this->assertDatabaseHas('invoices', [
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('memberships', [
            'id' => $membership->id,
            'amount' => 0,
        ]);
    }

    public function testCheckInWithExpiredMembership()
    {
        // Create a user with an expired membership.
        $user = User::factory()->create();

        $membership = new Membership([
            'user_id' => $user->id,
            'amount' => 1,
            'status' => 'Active',
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->toDateString(),
        ]);
        $membership->save();

        $response = $this->json('POST', route('user.checkin',$user->id));

        $response->assertJson([
            'success' => false,
            'message' => 'Membership date is due.',
        ]);

        // Check that no invoice was created and the user's membership amount was not updated.
        $this->assertDatabaseMissing('invoices', [
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('memberships', [
            'id' => $membership->id,
            'amount' => 1,
        ]);
    }

    public function testCheckInWithNoCredits()
    {
        // Create a user with an expired membership.
        $user = User::factory()->create();

        $membership = new Membership([
            'user_id' => $user->id,
            'amount' => 0,
            'status' => 'Active',
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addMonths(1)->toDateString(),
        ]);
        $membership->save();

        $response = $this->json('POST', route('user.checkin',$user->id));

        $response->assertJson([
            "success" => false,
            "message" => "No more credits",
        ]);

        // Check that no invoice was created and the user's membership amount was not updated.
        $this->assertDatabaseMissing('invoices', [
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('memberships', [
            'id' => $membership->id,
            'amount' => 0,
        ]);
    }

    public function testCheckInWithCanceledMembership()
    {
        // Create a user with an expired membership.
        $user = User::factory()->create();

        $membership = new Membership([
            'user_id' => $user->id,
            'amount' => 16,
            'status' => 'Canceled',
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addMonths(1)->toDateString(),
        ]);
        $membership->save();

        $response = $this->json('POST', route('user.checkin',$user->id));

        $response->assertJson([
            "success" => false,
            "message" => "Membership is canceled.",
        ]);

        // Check that no invoice was created and the user's membership amount was not updated.
        $this->assertDatabaseMissing('invoices', [
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('memberships', [
            'id' => $membership->id,
            'amount' => 16,
            'status' => 'Canceled',
        ]);
    }
    public function testCheckInWithoutMembership()
    {
        // Create a user without a membership.
        $user = User::factory()->create();

        $response = $this->json('POST', route('user.checkin',$user->id));

        $this->assertFalse($response->json()['success']);
        $this->assertEquals('Membership not found.', $response->json()['message']);
    }
}
