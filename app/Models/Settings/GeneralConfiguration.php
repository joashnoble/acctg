<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class GeneralConfiguration extends Model
{
    protected $table = 'account_integration';
    protected $primaryKey = 'integration_id';
    public $timestamps = false;
}