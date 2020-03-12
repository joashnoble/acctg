<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $table = 'company_info';
    protected $primaryKey = 'company_id';
    public $timestamps = false;
}