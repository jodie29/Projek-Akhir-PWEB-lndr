<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'service_id',
        'actual_weight',
        'total_price',
        'payment_method',
        'status'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
