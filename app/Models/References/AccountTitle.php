<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class AccountTitle extends Model
{
    protected $table = 'account_titles';
    protected $primaryKey = 'account_id';
    public $timestamps = false;
}
