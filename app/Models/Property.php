<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Property extends Model
{
   use HasFactory;

    protected $fillable = ['owner_id', 'name', 'address', 'description'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
