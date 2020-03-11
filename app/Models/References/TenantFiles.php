<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class TenantFiles extends Model
{
    protected $table = 'b_tenant_files';
    protected $primaryKey = 'file_id';
    public $timestamps = false;
}
