<?php

namespace App\Models\Financing;

use Illuminate\Database\Eloquent\Model;

class JournalAccounts extends Model
{
    protected $table = 'journal_accounts';
    protected $primaryKey = 'journal_account_id';
    public $timestamps = false;
}
