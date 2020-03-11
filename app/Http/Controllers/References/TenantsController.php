<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\References\Tenants;
use App\Models\References\TenantFiles;
use App\Models\References\Customers;
use App\Models\Transactions\ContractInfo;
use App\Models\Transactions\BillingInfo;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;
use Illuminate\Support\Facades\Storage;
use File;

class TenantsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tenants = Tenants::select(
                                '*',
                                DB::raw('"" as files')
                            )->where('is_deleted', 0)->orderBy('tenant_id', 'desc')->get();
        return Reference::collection($tenants);
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
                'trade_name' => 'required',
                'company_name' => 'required',
                'business_concept' => 'required',
                'head_office_address' => 'required',
                'billing_address' => 'required',
                'contact_person' => 'required',
                'designation' => 'required',
                'contact_number' => 'required',
                'tin_number' => 'required',
            ]
        )->validate();
        
        $tenant = new Tenants;
        $customer = new Customers;

        $tenant->tenant_code = DB::select("select CreateTenantCode() as tenant_code")[0]->tenant_code;
        $tenant->trade_name = $request->input('trade_name');
        $tenant->company_name = $request->input('company_name');
        $tenant->space_code = $request->input('space_code');
        $tenant->business_concept = $request->input('business_concept');
        $tenant->head_office_address = $request->input('head_office_address');
        $tenant->billing_address = $request->input('billing_address');
        $tenant->contact_person = $request->input('contact_person');
        $tenant->designation = $request->input('designation');
        $tenant->contact_number = $request->input('contact_number');
        $tenant->email_address = $request->input('email_address');
        $tenant->tin_number = $request->input('tin_number');
        $tenant->is_vatted = $request->input('is_vatted');
        $tenant->vat_percent = $request->input('vat_percent');
        $tenant->is_auto = $request->input('is_auto');
        $tenant->is_withhold_electricity = $request->input('is_withhold_electricity');
        $tenant->business_permit = $request->input('business_permit');
        $tenant->tenant_information_sheet = $request->input('tenant_information_sheet');
        $tenant->valid_id = $request->input('valid_id');
        $tenant->tin_cor = $request->input('tin_cor');
        $tenant->dti_sec = $request->input('dti_sec');
        $tenant->notarized_contract = $request->input('notarized_contract');
        $tenant->proof_of_billing = $request->input('proof_of_billing');
        $tenant->clearance_to_operate = $request->input('clearance_to_operate');
        $tenant->others = $request->input('others');
        $tenant->others_specify = $request->input('others_specify');
        $tenant->billing_notes = $request->input('billing_notes');
        $tenant->created_datetime = Carbon::now();
        $tenant->created_by = Auth::user()->id;

        // return json based from the resource data
        if($tenant->save()){
            $customer->tenant_id = $tenant->tenant_id;
            $customer->customer_name = $tenant->trade_name;
            $customer->contact_name = $tenant->contact_person;
            $customer->address = $tenant->head_office_address;
            $customer->email_address = $tenant->email_address;
            $customer->contact_no = $tenant->contact_number;
            $customer->customer_type_id = 0;
            $customer->tin_no = $tenant->tin_number;
            $customer->photo_path = 'assets/img/anonymous-icon.png';
            $customer->date_created = Carbon::now();
            $customer->posted_by_user = 0;
            $customer->office_fax_number = ''; 
            $customer->business_organization = '';
            $customer->ar_trans_id = 4;
            $customer->payment_term_desc = '';

            $customer->save();
            $tenant['files'] = '';
            return ( new Reference( $tenant ))
                ->response()
                ->setStatusCode(201);
        }
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
        $tenant = Tenants::findOrFail($id);

        return ( new Reference( $tenant ) )
            ->response()
            ->setStatusCode(200);
    }

    public function showFiles($id)
    {
        $tenant_file = TenantFiles::where('tenant_id', $id)->where('is_deleted', 0)->get();

        return Reference::collection($tenant_file);
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
                'trade_name' => 'required',
                'company_name' => 'required',
                'business_concept' => 'required',
                'head_office_address' => 'required',
                'billing_address' => 'required',
                'contact_person' => 'required',
                'designation' => 'required',
                'contact_number' => 'required',
                'tin_number' => 'required',
            ]
        )->validate();

        $tenant = Tenants::findOrFail($id);
        $customer = Customers::where('tenant_id', $id)->firstOrFail();

        $tenant->trade_name = $request->input('trade_name');
        $tenant->company_name = $request->input('company_name');
        $tenant->space_code = $request->input('space_code');
        $tenant->business_concept = $request->input('business_concept');
        $tenant->head_office_address = $request->input('head_office_address');
        $tenant->billing_address = $request->input('billing_address');
        $tenant->contact_person = $request->input('contact_person');
        $tenant->designation = $request->input('designation');
        $tenant->contact_number = $request->input('contact_number');
        $tenant->email_address = $request->input('email_address');
        $tenant->tin_number = $request->input('tin_number');
        $tenant->is_vatted = $request->input('is_vatted');
        $tenant->vat_percent = $request->input('vat_percent');
        $tenant->is_auto = $request->input('is_auto');
        $tenant->is_withhold_electricity = $request->input('is_withhold_electricity');
        $tenant->business_permit = $request->input('business_permit');
        $tenant->tenant_information_sheet = $request->input('tenant_information_sheet');
        $tenant->valid_id = $request->input('valid_id');
        $tenant->tin_cor = $request->input('tin_cor');
        $tenant->dti_sec = $request->input('dti_sec');
        $tenant->notarized_contract = $request->input('notarized_contract');
        $tenant->proof_of_billing = $request->input('proof_of_billing');
        $tenant->clearance_to_operate = $request->input('clearance_to_operate');
        $tenant->others = $request->input('others');
        $tenant->others_specify = $request->input('others_specify');
        $tenant->billing_notes = $request->input('billing_notes');
        $tenant->modified_datetime = Carbon::now();
        $tenant->modified_by = Auth::user()->id;

        if($tenant->save()){
            $customer->customer_name = $tenant->trade_name;
            $customer->contact_name = $tenant->contact_person;
            $customer->address = $tenant->head_office_address;
            $customer->email_address = $tenant->email_address;
            $customer->contact_no = $tenant->contact_number;
            $customer->tin_no = $tenant->tin_number;
            $customer->date_modified = Carbon::now();

            $customer->save();

            return ( new Reference( $tenant ))
                ->response()
                ->setStatusCode(201);
        }

        //return json based from the resource data
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
        $tenant = Tenants::findOrFail($id);
        $tenant->is_deleted = 1;
        $tenant->deleted_datetime = Carbon::now();
        $tenant->deleted_by = Auth::user()->id;

        //update classification based on the http json body that is sent
        $tenant->save();

        return ( new Reference( $tenant ) )
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

    public function tenantHistory($id)
    {
        return DB::select('CALL tenant_history('.$id.')');
    }

    public function fileupload(Request $request)
    {
        if ($request->file->isValid()) {
            $uploadedFile = $request->file;
            $uploadedPath = $request->path;
            $uploadedFolder = $request->folder;

            if(file_exists($uploadedPath.'/'.$uploadedFolder.'/'.$uploadedFile->getClientOriginalName())){
                $uploadedFile->move($uploadedPath.'/'.$uploadedFolder, $uploadedFile->getClientOriginalName());
                return;
            }

            $uploadedFile->move($uploadedPath.'/'.$uploadedFolder, $uploadedFile->getClientOriginalName());

            $file = new TenantFiles;
            $file->tenant_id = $uploadedFolder;
            $file->file_name = $uploadedFile->getClientOriginalName();
            $file->file_path = $uploadedPath.'/'.$uploadedFolder;
            $file->save();

            return response(['status'=>'success', 'name'=>$uploadedFile->getClientOriginalName(), 'path'=>$uploadedPath.'/'.$uploadedFolder, 'id'=>$file->file_id], 200);
        }
    }

    public function filedelete(Request $request)
    {
        if(File::delete($request->path)){

            $file = TenantFiles::findOrFail($request->file_id);
            $file->is_deleted = 1;
            $file->save();

            return response(['status'=>'success', 'message'=>'Deleted Successfully', 'title'=>'Success'], 200);
        }
        return response(['status'=>'error', 'message'=>'Deleting Failed', 'title'=>'Error'], 200); 
    }

    public function checkIfUsed($id)
    {
        $exists = 'false';

        if(ContractInfo::where('tenant_id', '=', $id)
            ->where('is_deleted', 0)
            ->exists()) {
            $exists = 'true';
        }
        
        if(BillingInfo::where('tenant_id', '=', $id)
            ->where('is_deleted', 0)
            ->exists()) {
            $exists = 'true';
        }

        return $exists;
    }
}
