<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class AccountingPeriod extends Model
{
    protected $table = 'accounting_period';
    protected $primaryKey = 'accounting_period_id';
    public $timestamps = false;
}