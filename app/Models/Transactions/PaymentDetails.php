<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class PaymentDetails extends Model
{
    protected $table = 'b_payment_details';
    protected $primaryKey = 'payment_details_id';
    public $timestamps = false;
}
