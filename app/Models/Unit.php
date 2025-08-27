<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'name', 'price', 'capacity'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
