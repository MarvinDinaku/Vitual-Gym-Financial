<?php

use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\InvoiceLinesController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\UserController;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('user/{id}/checkin', [UserController::class, 'CheckIn'])->name('user.checkin');


Route::get('memberships', [MembershipController::class, 'index'])->name('memberships');
Route::post('memberships/store', [MembershipController::class, 'store'])->name('memberships.store');
Route::get('memberships/{id}', [MembershipController::class, 'show'])->name('memberships.show');
Route::put('memberships/{id}/disable', [MembershipController::class, 'disable'])->name('memberships.disable');
Route::put('memberships/{id}/updateAmount', [MembershipController::class, 'updateAmount'])->name('memberships.update_amount');
Route::delete('memberships/{id}/destroy', [MembershipController::class, 'destroy'])->name('memberships.destroy');


Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices');
Route::post('invoices/store', [InvoiceController::class, 'store'])->name('invoices.store');
Route::get('invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
Route::put('invoices/{id}/update', [InvoiceController::class, 'update'])->name('invoices.update');
Route::post('invoices/{id}/storeInvoiceLine', [InvoiceController::class, 'storeByInvoiceLine'])->name('invoices.store_by_id');
Route::delete('invoices/{id}/destroy', [InvoiceController::class, 'destroy'])->name('invoices.destroy');




