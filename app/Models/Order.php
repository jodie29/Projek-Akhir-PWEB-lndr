<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsDateTime;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_name',
        'customer_phone',
        'pickup_address',
        'address',
        'service_id',
        'actual_weight',
        'total_price',
        'payment_method',
        'status',
        'courier_id',
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

    public function customer()
    {
        return $this->belongsTo(\App\Models\User::class, 'customer_id');
    }

    public function courier()
    {
        return $this->belongsTo(\App\Models\User::class, 'courier_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function collectedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'collected_by');
    }

    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }

    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->status)) {
                $order->status = 'pending';
            }
        });

        // Prevent status change to 'processing' for Bayar Nanti orders unless the customer confirmed
        static::saving(function ($order) {
            // If someone tries to set it to 'processing' while it's Bayar Nanti and unconfirmed, revert to awaiting_confirmation
            if ($order->isDirty('status') && $order->status === 'processing') {
                if (($order->payment_method ?? '') === 'Bayar Nanti' && ! ($order->customer_confirmed ?? false)) {
                    \Illuminate\Support\Facades\Log::warning('Prevented unauthorized status change to processing for Order: ' . ($order->id ?? '(new)'). '. Payment method Bayar Nanti and not confirmed.');
                    $order->status = 'awaiting_confirmation';
                }
            }
        });
    }
}
