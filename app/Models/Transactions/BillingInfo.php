<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class BillingInfo extends Model
{
    protected $table = 'b_billing_info';
    protected $primaryKey = 'billing_id';
    public $timestamps = false;
}
