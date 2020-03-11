<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{
    // accounting
    protected $table = 'departments';
    protected $primaryKey = 'department_id';
    public $timestamps = false;
}
