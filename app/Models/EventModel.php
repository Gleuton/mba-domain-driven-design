<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventModel extends Model
{
    protected $table = 'events';
    protected $fillable = [
        'id', 'name', 'description', 'date', 'is_published',
        'total_spots', 'total_spots_reserved', 'partner_id'
    ];
    protected $keyType = 'string';
    public $incrementing = false;

    public function partner(): BelongsTo
    {
        return $this->belongsTo(PartnerModel::class, 'partner_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(EventSectionModel::class, 'event_id');
    }
}