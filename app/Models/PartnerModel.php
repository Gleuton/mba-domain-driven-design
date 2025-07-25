<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerModel extends Model
{
    protected $table = 'partners';
    protected $fillable = ['id', 'name'];
    protected $keyType = 'string';
    public $incrementing = false;
}