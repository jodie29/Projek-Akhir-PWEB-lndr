<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsDateTime;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'service_id',
        'actual_weight',
        'total_price',
        'payment_method',
        'status',
        'approved_by',
        'approved_at',
        'collected_by',
        'collected_at',
        'collected_amount',
        'collection_method'
        ,'confirmation_token','customer_confirmed','customer_confirmed_at','is_walk_in'
    ];

    protected $casts = [
        'customer_confirmed' => 'boolean',
        'approved_at' => 'datetime',
        'collected_at' => 'datetime',
        'customer_confirmed_at' => 'datetime',
        'is_walk_in' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function collectedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'collected_by');
    }

    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->status)) {
                $order->status = 'pending';
            }
        });
    }
}
