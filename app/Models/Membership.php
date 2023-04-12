<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\MembershipStatusEnums;

class Membership extends Model
{
    use HasFactory;

    // The fillable property specifies which attributes are mass assignable
    protected $fillable = [
        'status',
        'user_id',
        'amount',
        'start_date',
        'end_date'
    ];

    // Define a relationship between the Membership and User models
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Add any custom methods or accessors/mutators here
}
