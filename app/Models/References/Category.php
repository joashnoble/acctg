<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'b_refcategory';
    protected $primaryKey = 'category_id';
    public $timestamps = false;
}
