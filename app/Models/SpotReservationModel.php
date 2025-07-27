<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpotReservationModel extends Model
{
    protected $table = 'spot_reservations';
    protected $fillable = ['spot_id', 'reservation_date', 'customer_id'];
    protected $keyType = 'string';

    protected $primaryKey = 'spot_id';
    public $incrementing = false;

    public function spot(): BelongsTo
    {
        return $this->belongsTo(EventSpotModel::class, 'spot_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerModel::class, 'customer_id');
    }
}