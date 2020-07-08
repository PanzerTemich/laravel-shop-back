<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $primaryKey = 'SKU';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}
