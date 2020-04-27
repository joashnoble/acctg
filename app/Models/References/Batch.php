<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = 'batch_info';
    protected $primaryKey = 'batch_id';
    public $timestamps = false;
}