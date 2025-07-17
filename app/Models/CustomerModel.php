<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $fillable = ['id', 'cpf', 'name'];
    protected $keyType = 'string';
    public $incrementing = false;
}