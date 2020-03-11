<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class StartBilling extends Model
{
    protected $table = 'b_start_billing';
    protected $primaryKey = 'start_billing_id';
    public $timestamps = false;
}
