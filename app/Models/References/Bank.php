<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'bank';
    protected $primaryKey = 'bank_id';
    public $timestamps = false;
}