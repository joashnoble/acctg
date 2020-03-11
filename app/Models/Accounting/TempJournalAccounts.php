<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class TempJournalAccounts extends Model
{
    protected $table = 'temp_journal_accounts';
    protected $primaryKey = 'temp_journal_account_id';
    public $timestamps = false;
}
