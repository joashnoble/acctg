<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\References\AccountTitle;
use App\Models\Settings\AccountingPeriod;
use App\Http\Resources\Reference;
use DB;
class AccountingPeriodController extends Controller
{

    public function index()
    {
        $accountingperiod=AccountingPeriod::all();

        $accountingperiod = AccountingPeriod::select(
            DB::raw("accounting_period.*"),
            DB::raw("CONCAT(DATE_FORMAT(accounting_period.period_start,'%M %d, %Y'),' to ',DATE_FORMAT(accounting_period.period_end,'%M %d, %Y')) as date_covered"),
            DB::raw("CONCAT_WS(' ',user_accounts.user_fname,user_accounts.user_lname) as user")
            )
            ->leftJoin('user_accounts', 'user_accounts.user_id', '=', 'accounting_period.closed_by_user')
            ->get()
            ;
        return Reference::collection($accountingperiod)
        ->response()
        ->setStatusCode(200);
    }


}
