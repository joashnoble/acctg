<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'b_reflocations';
    protected $primaryKey = 'location_id';
    public $timestamps = false;
    protected  $fillable = [
        'location_code',
        'location_desc'
    ];
}
