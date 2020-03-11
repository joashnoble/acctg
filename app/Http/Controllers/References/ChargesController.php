<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\References\Charges;
use App\Models\Transactions\BillingMiscCharges;
use App\Models\Transactions\BillingOthrCharges;
use App\Models\Transactions\BillingUtilCharges;
use App\Models\Transactions\ContractMiscCharges;
use App\Models\Transactions\ContractOthrCharges;
use App\Models\Transactions\ContractUtilCharges;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Auth;

class ChargesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $charges = Charges::leftJoin('account_titles', 'account_titles.account_id', '=', 'b_refcharges.account_id')
                        ->where('b_refcharges.is_deleted', 0)->orderBy('charge_id', 'desc')->get();
        return Reference::collection($charges);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Validator::make($request->all(),
            [
                'charge_code' => 'required',
                'charge_desc' => 'required',
                'account_id' => 'required|not_in:0'
            ], ['not_in' => 'The :attribute field is required.']
        )->setAttributeNames([
                'account_id' => 'account title']
        )->validate();

        $charge = new Charges();
        $charge->charge_code = $request->input('charge_code');
        $charge->charge_desc = $request->input('charge_desc');
        $charge->account_id = $request->input('account_id');
        $charge->sort = $request->input('sort');
        $charge->created_datetime = Carbon::now();
        $charge->created_by = Auth::user()->id;
        
        $charge->save();

        $data = Charges::leftJoin('account_titles', 'account_titles.account_id', '=', 'b_refcharges.account_id')
                        ->findOrFail($charge->charge_id);
        //return json based from the resource data
        return ( new Reference( $data ))
                ->response()
                ->setStatusCode(201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $charge = Charges::findOrFail($id);

        return ( new Reference( $charge ) )
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Validator::make($request->all(),
        [
            'charge_code' => 'required',
            'charge_desc' => 'required',
            'account_id' => 'required|not_in:0'
        ], ['not_in' => 'The :attribute field is required.']
        )->setAttributeNames([
                'account_id' => 'account title']
        )->validate();

        $charge = Charges::findOrFail($id);
        $charge->charge_code = $request->input('charge_code');
        $charge->charge_desc = $request->input('charge_desc');
        $charge->account_id = $request->input('account_id');
        $charge->sort = $request->input('sort');
        $charge->modified_datetime = Carbon::now();
        $charge->modified_by = Auth::user()->id;

        $charge->save();

        $data = Charges::leftJoin('account_titles', 'account_titles.account_id', '=', 'b_refcharges.account_id')
                        ->findOrFail($charge->charge_id);
        //return json based from the resource data
        return ( new Reference( $data ))
                ->response()
                ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage for deleting.
     * preventing force delete rather update the is_deleted = true
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $charge = Charges::findOrFail($id);
        $charge->is_deleted = 1;
        $charge->deleted_datetime = Carbon::now();
        $charge->deleted_by = Auth::user()->id;

        //update classification based on the http json body that is sent
        $charge->save();

        return ( new Reference( $charge ) )
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function checkIfUsed($id)
    {
        $exists = 'false';

        if(ContractMiscCharges::where('charge_id', '=', $id)
            ->leftJoin('b_contract_info', 'b_contract_info.contract_id', '=', 'b_contract_misc_charges.contract_id')
            ->where('b_contract_info.is_deleted', 0)
            ->exists()) {
            $exists = 'true';
        }

        if(ContractOthrCharges::where('charge_id', '=', $id)
            ->leftJoin('b_contract_info', 'b_contract_info.contract_id', '=', 'b_contract_othr_charges.contract_id')
            ->where('b_contract_info.is_deleted', 0)
            ->exists()) {
            $exists = 'true';
        }

        if(ContractUtilCharges::where('charge_id', '=', $id)
            ->leftJoin('b_contract_info', 'b_contract_info.contract_id', '=', 'b_contract_util_charges.contract_id')
            ->where('b_contract_info.is_deleted', 0)
            ->exists()) {
            $exists = 'true';
        }
        
        if(BillingMiscCharges::where('charge_id', '=', $id)
            ->leftJoin('b_billing_info', 'b_billing_info.billing_id', '=', 'b_billing_misc_charges.billing_id')
            ->where('b_billing_info.is_deleted', 0)
            ->exists()) {
            $exists = 'true';
        }

        if(BillingOthrCharges::where('charge_id', '=', $id)
            ->leftJoin('b_billing_info', 'b_billing_info.billing_id', '=', 'b_billing_othr_charges.billing_id')
            ->where('b_billing_info.is_deleted', 0)
            ->exists()) {
            $exists = 'true';
        }

        if(BillingUtilCharges::where('charge_id', '=', $id)
            ->leftJoin('b_billing_info', 'b_billing_info.billing_id', '=', 'b_billing_util_charges.billing_id')
            ->where('b_billing_info.is_deleted', 0)
            ->exists()) {
            $exists = 'true';
        }
        
        return $exists;
    }
}
