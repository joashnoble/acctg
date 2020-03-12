<?php

namespace App\Models\Financing;

use Illuminate\Database\Eloquent\Model;

class JournalInfo extends Model
{
    protected $table = 'journal_info';
    protected $primaryKey = 'journal_id';
    public $timestamps = false;
}
