<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'price_per_kg', 'active'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
