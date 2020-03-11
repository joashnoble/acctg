<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    protected $table = 'b_reffeetype';
    protected $primaryKey = 'fee_type_id';
    public $timestamps = false;
}
