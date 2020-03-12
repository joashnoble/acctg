<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    protected $table = 'customer_type';
    protected $primaryKey = 'customer_type_id';
    public $timestamps = false;
}