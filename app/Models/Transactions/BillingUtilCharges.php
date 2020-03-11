<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class BillingUtilCharges extends Model
{
    protected $table = 'b_billing_util_charges';
    protected $primaryKey = 'billing_util_id';
    public $timestamps = false;
}
