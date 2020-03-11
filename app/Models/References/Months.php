<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class Months extends Model
{
    protected $table = 'b_refmonths';
    protected $primaryKey = 'month_id';
    public $timestamps = false;
}
