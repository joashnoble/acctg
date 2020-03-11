<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class ContractType extends Model
{
    protected $table = 'b_refcontracttype';
    protected $primaryKey = 'contract_type_id';
    public $timestamps = false;
    protected  $fillable = [
        'contract_type_code',
        'contract_type_desc'
    ];
}
