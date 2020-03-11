<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class ChargeSlip extends Model
{
    protected $table = 'b_charge_slip';
    protected $primaryKey = 'charge_slip_id';
    public $timestamps = false;
}
