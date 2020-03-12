<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';
    protected $primaryKey = 'location_id';
    public $timestamps = false;
}
