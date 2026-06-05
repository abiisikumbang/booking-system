<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barber extends Model
{
    protected $fillable = ['name', 'is_active'];

    /**
     * Hubungan ke tabel bookings (One to Many)
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
