<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class PaymentInfo extends Model
{
    protected $table = 'b_payment_info';
    protected $primaryKey = 'payment_id';
    public $timestamps = false;
}
