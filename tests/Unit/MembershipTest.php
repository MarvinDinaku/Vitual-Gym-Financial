<?php

namespace Tests\Unit;

use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MembershipTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    public function testIndex()
    {
        $response = $this->json('GET', route('memberships'));

        $response->assertStatus(200);
    }

    public function testStore()
    {
        $data = [
            'amount' => 50,
            'status' => 'Active',
            'user_id' => 2,
        ];

        $response = $this->json('POST', route('memberships.store'),$data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Membership created successfully.',
                'data' => [
                    'amount' => $data['amount'],
                    'status' => $data['status'],
                    'user_id' => $data['user_id'],
                ]
            ]);

        $this->assertDatabaseHas('memberships', $data);
    }


    public function testShow()
    {
        // create a membership record in the database
        $membership = Membership::create([
            'amount' => 16,
            'status' => 'Active',
            'user_id' => 2,
            'start_date' => '2022-01-01 00:00:00',
            'end_date' => '2022-02-01 00:00:00',
        ]);

        // send a GET request to the show route for the membership
        $response = $this->json('GET', route('memberships.show',$membership->id));

        // assert that the response is successful and contains the expected data
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Membership retrieved successfully.',
                'data' => [
                    'id' => $membership->id,
                    'amount' => $membership->amount,
                    'status' => $membership->status,
                    'user_id' => $membership->user_id,
                    'start_date' => $membership->start_date,
                    'end_date' => $membership->end_date,
                ]
            ]);
    }

    public function testShowNotFound()
    {
        // send a GET request to the show route for a non-existent membership
        $response = $this->json('GET', route('memberships.show',999));

        // assert that the response is a 404 error
        $response->assertStatus(200)
            ->assertJson([
                'success' => false,
                'message' => 'Membership not found.',
            ]);
    }


    public function testUpdateAmount()
    {
        // Create a new membership for the user
        $membership = Membership::create([
            'user_id' => 1,
            'amount' => 10,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(1),
        ]);

        // Call the updateAmount() method for the created membership
        $response = $this->json('PUT', route('memberships.update_amount',1));

        // Assert that the response was successful
        $response->assertStatus(200);

        // Assert that the membership amount has been updated by 1
        $this->assertEquals($membership->amount - 1, Membership::find($membership->id)->amount);
    }


    public function testDestroy()
    {
        // Create a new membership for the user
        $membership = Membership::create([
            'user_id' => 2,
            'amount' => 10,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(1),
        ]);

        // Call the destroy() method for the created membership
        $response = $this->json('DELETE', route('memberships.destroy',$membership->id));

        // Assert that the response was successful
        $response->assertStatus(200);

        // Assert that the membership has been deleted
        $this->assertNull(Membership::find($membership->id));
    }
}
