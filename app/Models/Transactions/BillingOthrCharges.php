<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class BillingOthrCharges extends Model
{
    protected $table = 'b_billing_othr_charges';
    protected $primaryKey = 'billing_othr_id';
    public $timestamps = false;
}
