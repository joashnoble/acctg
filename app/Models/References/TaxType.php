<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class TaxType extends Model
{
    protected $table = 'tax_types';
    protected $primaryKey = 'tax_type_id';
    public $timestamps = false;
}