<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EventSpotModel extends Model
{
    protected $table = 'event_spots';
    protected $fillable = ['id', 'location', 'is_reserved', 'is_published', 'event_section_id'];
    protected $keyType = 'string';
    public $incrementing = false;

    public function section(): BelongsTo
    {
        return $this->belongsTo(EventSectionModel::class, 'event_section_id');
    }

    public function reservation(): HasOne
    {
        return $this->hasOne(SpotReservationModel::class, 'spot_id');
    }
}