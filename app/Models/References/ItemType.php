<?php

namespace App\Models\References;

use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    protected $table = 'item_types';
    protected $primaryKey = 'item_type_id';
    public $timestamps = false;
}