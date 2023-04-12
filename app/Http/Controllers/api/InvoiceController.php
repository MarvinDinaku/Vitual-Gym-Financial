<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceLines;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retrieve all invoices with their associated invoice lines
        $invoices = Invoice::with('invoice_lines')->get();
        // Return a JSON response with success status, message and invoice data
        return response()->json([
            "success" => true,
            "message" => "Invoices List",
            "data" => $invoices
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request fields
        $request->validate([
            'date' => 'required',
            'status' => 'required',
            'amount' => 'required',
        ]);

        // Create a new invoice object and set its properties
        $invoice= new Invoice([
            'date' => Carbon::now()->toDateString(),
            'status' => $request->get('status'),
            'user_id' => $request->get('user_id'),
            'amount' => $request->get('amount'),
            'description' =>$request->get('description'),
        ]);

        // Save the newly created invoice to the database
        $invoice->save();

        // Return a JSON response with success status, message and newly created invoice data
        return response()->json([
            "success" => true,
            "message" => "Invoice created successfully.",
            "data" => $invoice
        ]);
    }

    /**
     * Store a newly created invoice line resource in storage, associated with a particular invoice.
     */
    public function storeByInvoiceLine($id)
    {
        // Check if an invoice with the given user id and current month already exists
        $invoice = Invoice::whereUserId($id)->whereMonth('date', Carbon::now())->first();

        if(is_null($invoice)){
            // If no invoice exists for the user and current month, create a new invoice and associated invoice line
            $invoice= new Invoice([
                'date' => Carbon::now()->toDateString(),
                'status' => 'Outstanding',
                'user_id' => $id,
                'amount' => 0,
            ]);

            $invoice->save();

            $invoice_line= new InvoiceLines([
                'invoice_id' => $invoice->id,
                'amount' => 10,
                'description' =>'User checked',
            ]);
            $invoice->amount += $invoice_line->amount;
            $invoice->update();
            $invoice_line->save();

            // Return a JSON response with success status, message and newly created invoice line data
            return response()->json([
                "success" => true,
                "message" => "Invoice and Invoice Line created successfully.",
                "data" => $invoice_line
            ]);

        }else{
            // If an invoice already exists for the user and current month, create a new invoice line associated with that invoice
            $invoice_line= new InvoiceLines([
                'invoice_id' => $id,
                'amount' => 10,
                'description' =>'User checked',
            ]);
            $invoice->amount += $invoice_line->amount;
            $invoice->update();
            $invoice_line->save();

            // Return a JSON response with success status, message and newly created invoice line data
            return response()->json([
                "success" => true,
                "message" => "Invoice Line created successfully.",
                "data" => $invoice_line
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // retrieve the invoice by id along with its associated invoice lines
        $invoice = Invoice::with('invoice_lines')->whereId($id)->first();

        // if no invoice is found for the given id, return a JSON response indicating the error
        if (is_null($invoice)) {
            return response()->json([
                "success" => false,
                "message" => "Invoice not found."
            ]);
        }

        // if invoice is found, return a JSON response with the invoice data
        return response()->json([
            "success" => true,
            "message" => "Invoice retrieved successfully.",
            "data" => $invoice
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // validate the request data to ensure that the 'status' field is present
        $request->validate([
            'status' => 'required',
        ]);

        // find the invoice by id
        $invoice = Invoice::find($id);

        // set the status of the invoice to the value provided in the request data
        $invoice->status = $request->get('status');

        // save the changes to the invoice in the database
        $invoice->save();

        // return a JSON response indicating success and the updated invoice data
        return response()->json([
            "success" => true,
            "message" => "Invoice updated successfully.",
            "data" => $invoice
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // find the invoice by id
        $invoice = Invoice::find($id);

        // delete the invoice from the database
        $invoice->delete();

        // return a JSON response indicating success and the deleted invoice data
        return response()->json([
            "success" => true,
            "message" => "Invoice deleted successfully.",
            "data" => $invoice
        ]);
    }


}
