<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class BillingPeriod extends Model
{
    protected $table = 'b_refbillingperiod';
    protected $primaryKey = 'period_id';
    public $timestamps = false;
    protected  $fillable = [
        'period_start_date',
        'period_end_date',
        'month_id',
        'app_year',
        'period_due_date'
    ];
}
