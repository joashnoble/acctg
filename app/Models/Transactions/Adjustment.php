<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    protected $table = 'b_adjustments';
    protected $primaryKey = 'adjustment_id';
    public $timestamps = false;
}
