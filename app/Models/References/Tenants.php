<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class Tenants extends Model
{
    protected $table = 'b_tenants';
    protected $primaryKey = 'tenant_id';
    public $timestamps = false;
}
