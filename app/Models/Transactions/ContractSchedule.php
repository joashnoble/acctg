<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class ContractSchedule extends Model
{
    protected $table = 'b_contract_schedule';
    protected $primaryKey = 'contract_schedule_id';
    public $timestamps = false;
}
