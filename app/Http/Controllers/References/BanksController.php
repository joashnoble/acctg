<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\References\Bank;
use App\Http\Resources\Reference;
use DB;
use Illuminate\Support\Facades\Validator;
class BanksController extends Controller
{

    public function index()
    {
        return Reference::collection($this->GetBanks()->get())
        ->response()
        ->setStatusCode(200);
    }

    public function create(Request $request)
    {

        Validator::make($request->all(),
            [
                'bank_code' => 'required',
                'bank_name' => 'required',
                'account_number' => 'required',
                'account_type' => 'required|not_in:0',
            ],
            [
                'not_in' => 'The :attribute field is required.'
            ])
            ->setAttributeNames([
                'bank_code' => 'Bank Code',
                'bank_name' => 'Bank Name',
                'account_number' => 'Account Number',
                'account_type' => 'Account Type'
            ]
            )->validate();

        $bank = new Bank();
        $bank->bank_code = $request->input('bank_code');
        $bank->bank_name = $request->input('bank_name');
        $bank->account_number = $request->input('account_number');
        $bank->account_type = $request->input('account_type');
        $bank->save();

        return ( new Reference( $this->GetBanks($bank->bank_id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function show($id)
    {
        return ( new Reference($this->GetBanks($id)->get()[0]))
            ->response()
            ->setStatusCode(200);
        
    }

    public function update(Request $request,$id)
    {
        Validator::make($request->all(),
            [
                'bank_code' => 'required',
                'bank_name' => 'required',
                'account_number' => 'required',
                'account_type' => 'required|not_in:0',
            ],
            [
                'not_in' => 'The :attribute field is required.'
            ])
            ->setAttributeNames([
                'bank_code' => 'Bank Code',
                'bank_name' => 'Bank Name',
                'account_number' => 'Account Number',
                'account_type' => 'Account Type'
            ]
            )->validate();
        $bank = Bank::findOrFail($id);
        $bank->bank_code = $request->input('bank_code');
        $bank->bank_name = $request->input('bank_name');
        $bank->account_number = $request->input('account_number');
        $bank->account_type = $request->input('account_type');
        $bank->save();

        return ( new Reference( $this->GetBanks($id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function delete($id)
    {   
        $bank = Bank::findOrFail($id);
        $bank->is_deleted = 1;
        $bank->save();

        return ( new Reference( $bank ) )
            ->response()
            ->setStatusCode(200);
    }

    public function checkIfUsed()
    {
        $exists = 'false';
        return $exists;
    }

    public function GetBanks($id=null)
    {
        $bank = Bank::select(
            'bank_id',
            'bank_code',
            'bank_name',
            'account_number',
            'account_type',
            DB::raw('IF(account_type = 1, "Current Account", "Savings Account") as account_type_desc')
            )
            ->where('is_active',TRUE)
            ->where('is_deleted',FALSE);

            if($id!=null){ 
                $bank->where('bank_id',$id);
            }

        return $bank;
    }
}
