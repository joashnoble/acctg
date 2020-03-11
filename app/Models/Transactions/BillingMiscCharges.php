<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class BillingMiscCharges extends Model
{
    protected $table = 'b_billing_misc_charges';
    protected $primaryKey = 'billing_misc_id';
    public $timestamps = false;
}
