<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvoiceStatusEnums;

class Invoice extends Model
{
    use HasFactory;

    // The fillable property specifies which attributes are mass assignable
    protected $fillable = [
        'user_id',
        'date',
        'status',
        'description',
        'amount',
    ];

    // Define a relationship between the Invoice and InvoiceLines models
    public function invoice_lines(): HasMany
    {
        return $this->hasMany(InvoiceLines::class);
    }

    // Define a relationship between the Invoice and User models
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Add any custom methods or accessors/mutators here

    // Use $casts to specify any attribute that should be cast to a specific data type
    // protected $casts = [
    //    'status' => InvoiceStatusEnums::class
    // ];
}
