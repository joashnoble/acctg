<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'b_refdepartments';
    protected $primaryKey = 'department_id';
    public $timestamps = false;
    protected  $fillable = [
        'department_code',
        'department_desc'
    ];
}
