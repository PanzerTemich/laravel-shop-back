<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //La clave primaria es email
    //No va a ser autoincrementable
    //la clave primaria va a ser string
    //No va a controlar el timestamps
    protected $primaryKey = 'email';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}
