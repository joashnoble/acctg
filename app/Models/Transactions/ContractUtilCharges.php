<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class ContractUtilCharges extends Model
{
    protected $table = 'b_contract_util_charges';
    protected $primaryKey = 'contract_misc_id';
    public $timestamps = false;
}
