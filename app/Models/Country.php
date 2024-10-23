<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    /**
     * Get the address that owns the phone.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
