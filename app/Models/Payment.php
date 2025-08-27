<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
   use HasFactory;

    protected $fillable = ['reservation_id', 'payment_method', 'transaction_id', 'amount', 'status'];

    protected $casts = [
        'meta' => 'array',
        'paid_at' => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
