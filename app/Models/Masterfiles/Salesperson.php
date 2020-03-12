<?php

namespace App\Models\Masterfiles;

use Illuminate\Database\Eloquent\Model;

class Salesperson extends Model
{
    protected $table = 'salesperson';
    protected $primaryKey = 'salesperson_id';
    public $timestamps = false;
}