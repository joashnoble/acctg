<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\References\ContractType;
use App\Models\Transactions\ContractInfo;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Auth;

class ContractTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$departments = Department::where('is_deleted', 0)->orderBy('department_id', 'asc')->paginate(5);
        $contracttypes = ContractType::where('is_deleted', 0)->orderBy('contract_type_id', 'desc')->get();
        return Reference::collection($contracttypes);
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
                'contract_type_code' => 'required',
                'contract_type_desc' => 'required'
            ]
        )->validate();

        $contracttype = new ContractType();
        $contracttype->contract_type_code = $request->input('contract_type_code');
        $contracttype->contract_type_desc = $request->input('contract_type_desc');
        $contracttype->created_datetime = Carbon::now();
        $contracttype->created_by = Auth::user()->id;
    
        $contracttype->save();

        //return json based from the resource data
        return ( new Reference( $contracttype ))
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
        $contracttype = ContractType::findOrFail($id);

        return ( new Reference( $contracttype ) )
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
        $contracttype = ContractType::findOrFail($id);

        Validator::make($request->all(),
            [
                'contract_type_code' => 'required',
                'contract_type_desc' => 'required'
            ]
        )->validate();

        $contracttype->contract_type_code = $request->input('contract_type_code');
        $contracttype->contract_type_desc = $request->input('contract_type_desc');
        $contracttype->modified_datetime = Carbon::now();
        $contracttype->modified_by = Auth::user()->id;


        //update  based on the http json body that is sent
        $contracttype->update();

        return ( new Reference( $contracttype ) )
            ->response()
            ->setStatusCode(200);
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
        $contracttype = ContractType::findOrFail($id);
        $contracttype->is_deleted = 1;
        $contracttype->deleted_datetime = Carbon::now();
        $contracttype->deleted_by = Auth::user()->id;

        //update classification based on the http json body that is sent
        $contracttype->save();

        return ( new Reference( $contracttype ) )
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
        
    }

    public function checkIfUsed($id)
    {
        $exists = 'false';

        if(ContractInfo::where('contract_type_id', '=', $id)
            ->where('is_deleted', 0)
            ->exists()) {
            $exists = 'true';
        }
        
        return $exists;
    }
}
