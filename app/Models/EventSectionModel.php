<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventSectionModel extends Model
{
    protected $table = 'event_sections';
    protected $fillable = [
        'id', 'name', 'description', 'is_published',
        'total_spots', 'total_spots_reserved', 'price', 'event_id'
    ];
    protected $keyType = 'string';
    public $incrementing = false;

    public function event(): BelongsTo
    {
        return $this->belongsTo(EventModel::class, 'event_id');
    }

    public function spots(): HasMany
    {
        return $this->hasMany(EventSpotModel::class, 'event_section_id');
    }
}