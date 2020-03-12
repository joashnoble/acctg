<?php

namespace App\Http\Controllers\Masterfiles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Masterfiles\Supplier;
use App\Http\Resources\Reference;
use DB;
class SuppliersController extends Controller
{

    public function index()
    {
        return Reference::collection($this->GetSuppliers()->get())
        ->response()
        ->setStatusCode(200);
    }
    
    public function create(Request $request)
    {
        $supplier = new Supplier();
        $supplier->supplier_name = $request->input('supplier_name');
        $supplier->contact_name = $request->input('contact_name');
        $supplier->address = $request->input('address');
        $supplier->email_address = $request->input('email_address');
        $supplier->contact_no = $request->input('contact_no');
        $supplier->tin_no = $request->input('tin_no');
        // $supplier->photo_path = $request->input('photo_path');
        $supplier->tax_type_id = $request->input('tax_type_id');
        $supplier->tax_output = $request->input('tax_output');

        $supplier->save();

        return ( new Reference( $this->GetSuppliers($supplier->supplier_id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function show($id)
    {
        return ( new Reference($this->GetSuppliers($id)->get()[0]))
            ->response()
            ->setStatusCode(200);
        
    }

    public function update(Request $request,$id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->supplier_name = $request->input('supplier_name');
        $supplier->contact_name = $request->input('contact_name');
        $supplier->address = $request->input('address');
        $supplier->email_address = $request->input('email_address');
        $supplier->contact_no = $request->input('contact_no');
        $supplier->tin_no = $request->input('tin_no');
        // $supplier->photo_path = $request->input('photo_path');
        $supplier->tax_type_id = $request->input('tax_type_id');
        $supplier->tax_output = $request->input('tax_output');
        $supplier->save();

        return ( new Reference( $this->GetSuppliers($id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function delete($id)
    {   
        $supplier = Supplier::findOrFail($id);
        $supplier->is_deleted = 1;
        $supplier->save();

        return ( new Reference( $supplier ) )
            ->response()
            ->setStatusCode(200);
    }

    public function checkIfUsed()
    {
        $exists = 'false';
        return $exists;
    }

    public function journalSuppliers(){
        $suppliers = Supplier::select(
            DB::raw("CONCAT('S-',supplier_id) as particular_id"),
            'supplier_name',
            )
            ->orderBy('suppliers.supplier_name','ASC')
            ->where('suppliers.is_active',TRUE)
            ->where('suppliers.is_deleted',FALSE);

            return ( new Reference($suppliers->get()))
            ->response()
            ->setStatusCode(200);
    }

    public function GetSuppliers($id=null) // List only, without on hand
    {
        $suppliers = Supplier::select(
            'supplier_id',
            'supplier_name',
            'contact_name',
            'address',
            'email_address',
            'contact_no',
            'tin_no',
            'photo_path',
            'tax_type_id',
            'tax_output',
            'posted_by_user',
            )
            ->orderBy('suppliers.supplier_name','ASC')
            ->where('suppliers.is_active',TRUE)
            ->where('suppliers.is_deleted',FALSE)
            
            ;

            if($id!=null){ 
                $suppliers->where('supplier_id',$id);
            }

        return $suppliers;
    }

}
