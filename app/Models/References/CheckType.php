<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class CheckType extends Model
{
    protected $table = 'b_refchecktype';
    protected $primaryKey = 'check_type_id';
    public $timestamps = false;
}
