<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class StatementOfAccountSetting extends Model
{
    protected $table = 'soa_settings';
    protected $primaryKey = 'soa_settings_id';
    public $timestamps = false;
}