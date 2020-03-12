<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\References\AccountType;
use App\Http\Resources\Reference;

class AccountTypesController extends Controller
{

    public function index()
    {
        $accounttype = AccountType::select(
            'account_type_id',
            'account_type',
            'description'
            )
            ->orderBy('account_type_id','asc')
            ;
        return Reference::collection($accounttype->get())
        ->response()
        ->setStatusCode(200);
    }

}
