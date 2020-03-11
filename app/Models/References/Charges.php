<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class Charges extends Model
{
    protected $table = 'b_refcharges';
    protected $primaryKey = 'charge_id';
    public $timestamps = false;

    // public function account_title()
    // {
    //     return $this->hasOne('App\Models\References\AccountTy');
    // }
}
