<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class AccountClass extends Model
{
    protected $table = 'account_classes';
    protected $primaryKey = 'account_class_id';
    public $timestamps = false;
}
