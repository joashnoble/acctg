<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class BillingAdjustments extends Model
{
    protected $table = 'b_billing_adjustments';
    protected $primaryKey = 'billing_adjustment_id';
    public $timestamps = false;
}
