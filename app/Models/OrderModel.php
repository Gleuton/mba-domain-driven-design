<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $fillable = ['id', 'amount', 'status', 'customer_id', 'event_spot_id'];
    protected $keyType = 'string';
    public $incrementing = false;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerModel::class, 'customer_id');
    }

    public function spot(): BelongsTo
    {
        return $this->belongsTo(EventSpotModel::class, 'event_spot_id');
    }
}