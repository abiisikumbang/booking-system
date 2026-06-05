<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Booking extends Model
{
    protected $fillable = [
        'barber_id',
        'customer_name',
        'customer_phone',
        'booking_date',
        'booking_slot',
        'booking_code',
        'status'
    ];

    /**
     * Hubungan balik ke tabel barbers (Belongs To)
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }
}
