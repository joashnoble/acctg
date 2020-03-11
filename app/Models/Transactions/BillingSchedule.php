<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class BillingSchedule extends Model
{
    protected $table = 'b_billing_schedule';
    protected $primaryKey = 'billing_schedule_id';
    public $timestamps = false;
}
