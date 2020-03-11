<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class ContractOtherFees extends Model
{
    protected $table = 'b_contract_other_fees';
    protected $primaryKey = 'fee_id';
    public $timestamps = false;
}
