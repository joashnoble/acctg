<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class AssetSetting extends Model
{
    protected $table = 'asset_settings';
    protected $primaryKey = 'asset_settings_id';
    public $timestamps = false;
}