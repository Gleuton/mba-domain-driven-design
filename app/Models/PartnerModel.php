<?php

namespace App\Models;

use App\Common\Domain\ValueObjects\Name;
use App\Events\Domain\Entities\PartnerId;
use Illuminate\Database\Eloquent\Model;

class PartnerModel extends Model
{
    protected $table = 'partners';
    protected $fillable = ['id', 'name'];
    protected $keyType = 'uuid';
    public $incrementing = false;
}