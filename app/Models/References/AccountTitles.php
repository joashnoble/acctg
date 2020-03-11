<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class AccountTitles extends Model
{
    protected $table = 'account_titles';
    protected $primaryKey = 'account_id';
    public $timestamps = false;
}
