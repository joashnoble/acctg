<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class NatureOfBusiness extends Model
{
    protected $table = 'b_refnatureofbusiness';
    protected $primaryKey = 'nature_of_business_id';
    public $timestamps = false;
    protected  $fillable = [
        'nature_of_business_code',
        'nature_of_business_desc'
    ];
}
