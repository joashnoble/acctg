<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class StopBilling extends Model
{
    protected $table = 'b_stop_billing';
    protected $primaryKey = 'stop_billing_id';
    public $timestamps = false;
}
