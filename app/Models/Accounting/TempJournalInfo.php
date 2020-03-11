<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class TempJournalInfo extends Model
{
    protected $table = 'temp_journal_info';
    protected $primaryKey = 'temp_journal_id';
    public $timestamps = false;
}
