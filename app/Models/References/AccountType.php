<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    protected $table = 'account_types';
    protected $primaryKey = 'account_type_id';
    public $timestamps = false;
}
