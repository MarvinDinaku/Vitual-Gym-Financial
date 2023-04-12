<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\InvoiceController;
use App\Models\Invoice;
use App\Models\InvoiceLines;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function testIndexInvoice()
    {
        // Create a new invoice instance
        $invoice = new Invoice([
            'date' => Carbon::now()->toDate(),
            'status' => 'paid',
            'user_id' => 1,
            'amount' => 100,
            'description' => 'Test invoice',
        ]);

        // Save the invoice instance
        $invoice->save();

        // Call the index method and assert that the response contains the invoice data
        $response = $this->json('GET', route('invoices'));
        $response->assertStatus(200);
    }

    public function testStoreInvoice()
    {
        // Create a fake request with required fields
        $request = new Request([
            'date' => '2023-04-09',
            'status' => 'paid',
            'amount' => 0,
            'user_id' => 1,
            'description' => 'Test invoice',
        ]);

        // Create a new instance of the controller
        $controller = new InvoiceController();

        // Call the store method with the fake request object
        $response = $controller->store($request);

        $this->assertTrue($response->getData()->success);
        $this->assertEquals('Invoice created successfully.', $response->getData()->message);

        // Check that the invoice object was created with the expected data
        $invoice = $response->getData()->data;
        $this->assertEquals(Carbon::now()->toDateString(), $invoice->date);
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(0, $invoice->amount);
        $this->assertEquals('Test invoice', $invoice->description);
        $this->assertEquals(1, $invoice->user_id);
    }

    public function testShowInvoice()
    {

        $invoice = Invoice::create([
            'user_id' =>1,
            'date' => '2023-04-09',
            'status' => 'Paid',
            'description' => 'Invoice for services rendered',
            'amount' => 100.00,
        ]);

        // Create two invoice lines and attach them to the invoice
        $invoiceLine1 = InvoiceLines::create([
            'invoice_id' => $invoice->id,
            'description' => 'Product 1',
            'amount' => 50.00,
        ]);

        $invoiceLine2 = InvoiceLines::create([
            'invoice_id' => $invoice->id,
            'description' => 'Product 2',
            'amount' => 20.00,
        ]);

        // Send a GET request to the /invoices/{id} endpoint
        $response = $this->json('GET', route('invoices.show',$invoice->id));

        // Assert that the response has a status code of 200
        $response->assertStatus(200);

        // Assert that the response contains the correct data
        $response->assertJson([
            'success' => true,
            'message' => 'Invoice retrieved successfully.',
            'data' => [
                'id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'date' => $invoice->date,
                'status' => $invoice->status,
                'description' => $invoice->description,
                'amount' => $invoice->amount,
                'invoice_lines' => [
                    [
                        'id' => $invoiceLine1->id,
                        'invoice_id' => $invoice->id,
                        'description' => 'Product 1',
                        'amount' => 50.00,
                    ],
                    [
                        'id' => $invoiceLine2->id,
                        'invoice_id' => $invoice->id,
                        'description' => 'Product 2',
                        'amount' => 20.00,
                    ],
                ]
            ]
        ]);

        // Assert that a non-existent invoice returns a 404 response
        $response = $this->get('/invoices/' . 'non-existent-id');
        $response->assertStatus(404);
    }


    public function testUpdateInvoice()
    {
        // Create an invoice in the database
        $invoice = Invoice::create([
            'user_id' => 1,
            'date' => '2023-04-09',
            'status' => 'Outstanding',
            'description' => 'Invoice for services rendered',
            'amount' => 100.0,
        ]);

        // Make a PUT request to update the invoice status
        $response = $this->json('PUT', route('invoices.update',$invoice->id), [
            'status' => 'Paid'
        ]);

        // Assert response status code is 200
        $response->assertStatus(200);

        // Assert response contains expected JSON data
        $response->assertJson([
            'success' => true,
            'message' => 'Invoice updated successfully.',
            'data' => [
                'id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'date' => $invoice->date,
                'status' => 'Paid', // Check that status was updated
                'description' => $invoice->description,
                'amount' => $invoice->amount,
            ]
        ]);

        // Assert that the invoice status was updated in the database
        $updatedInvoice = Invoice::find($invoice->id);
        $this->assertEquals('Paid', $updatedInvoice->status);
    }


    public function testDeleteInvoice()
    {
        // Create an invoice to delete
        $invoice = new Invoice([
            'user_id' => 1,
            'date' => '2023-04-09',
            'status' => 'Paid',
            'description' => 'Invoice for services rendered',
            'amount' => 100.0,
        ]);
        $invoice->save();

        // Call the destroy method
        $response = $this->json('DELETE', route('invoices.destroy',$invoice->id));

        // Assert that the invoice was deleted and the response is correct
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Invoice deleted successfully.',
            'data' => [
                'id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'date' => $invoice->date,
                'status' => $invoice->status,
                'description' => $invoice->description,
                'amount' => $invoice->amount,
            ],
        ]);
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

//    public function testStoreByInvoiceLine()
//    {
//        $userId =1;
//
//        // Call the storeByInvoiceLine method and check the response
//        $response = $this->json('POST', route('invoices.store_by_id',$userId));
//        $response->assertStatus(200);
//        $response->assertJson([
//            "success" => true,
//            "message" => "Invoice and Invoice Line created successfully.",
//            "data" => [
//                "invoice_id" => 1,
//                "amount" => 10,
//                "description" => "User checked",
//            ]
//        ]);
//
//        // Check that the invoice and invoice line were created in the database
//        $this->assertDatabaseHas('invoices', [
//            'user_id' => $userId,
//            'status' => 'Outstanding',
//            'amount' => 10,
//        ]);
//
//        $this->assertDatabaseHas('invoice_lines', [
//            'invoice_id' => 1,
//            'amount' => 10,
//            'description' => 'User checked',
//        ]);
//
//        // Call the method again for the same user and check the response
//        $response = $this->json('POST', route('invoices.store_by_id',$userId));
//        $response->assertStatus(200);
//        $response->assertJson([
//            "success" => true,
//            "message" => "Invoice and Invoice Line created successfully.",
//            "data" => [
//                "invoice_id" => 1,
//                "amount" => 10,
//                "description" => "User checked",
//            ]
//        ]);
//
//        // Check that only one invoice was created for the user
//        $this->assertEquals(1, Invoice::whereUserId($userId)->count());
//
//        // Check that the invoice amount was updated correctly
//        $this->assertEquals(20, Invoice::whereUserId($userId)->first()->amount);
//    }
}
