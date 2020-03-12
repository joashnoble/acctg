<?php

namespace App\Http\Controllers\Masterfiles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Masterfiles\Customer;
use App\Http\Resources\Reference;
use DB;
class CustomersController extends Controller
{

    public function index()
    {
        return Reference::collection($this->GetCustomers()->get())
        ->response()
        ->setStatusCode(200);
    }
    
    public function create(Request $request)
    {
        $customer = new Customer();
        $customer->customer_name = $request->input('customer_name');
        $customer->contact_name = $request->input('contact_name');
        $customer->address = $request->input('address');
        $customer->email_address = $request->input('email_address');
        $customer->contact_no = $request->input('contact_no');
        $customer->tin_no = $request->input('tin_no');
        $customer->photo_path = $request->input('photo_path');
        $customer->customer_type_id = $request->input('customer_type_id');
        $customer->save();

        return ( new Reference( $this->GetCustomers($customer->customer_id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function show($id)
    {
        return ( new Reference($this->GetCustomers($id)->get()[0]))
            ->response()
            ->setStatusCode(200);
        
    }

    public function update(Request $request,$id)
    {
        $customer = Customer::findOrFail($id);
        $customer->customer_name = $request->input('customer_name');
        $customer->contact_name = $request->input('contact_name');
        $customer->address = $request->input('address');
        $customer->email_address = $request->input('email_address');
        $customer->contact_no = $request->input('contact_no');
        $customer->tin_no = $request->input('tin_no');
        $customer->photo_path = $request->input('photo_path');
        $customer->customer_type_id = $request->input('customer_type_id');
        $customer->save();

        return ( new Reference( $this->GetCustomers($id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function delete($id)
    {   
        $customer = Customer::findOrFail($id);
        $customer->is_deleted = 1;
        $customer->save();

        return ( new Reference( $customer ) )
            ->response()
            ->setStatusCode(200);
    }

    public function checkIfUsed()
    {
        $exists = 'false';
        return $exists;
    }

    public function  journalcustomers(){
        $customers = Customer::select(
            DB::raw("CONCAT('C-',customer_id) as particular_id"),
            'customer_name',
            )
            ->orderBy('customers.customer_name','ASC')
            ->where('is_active',TRUE)
            ->where('is_deleted',FALSE)
            ;
            return ( new Reference($customers->get()))
            ->response()
            ->setStatusCode(200);
    }

    public function GetCustomers($id=null) // List only, without on hand
    {
        $customers = Customer::select(
            'customer_id',
            'customer_name',
            'contact_name',
            'address',
            'email_address',
            'customer_type_id',
            'contact_no',
            'tin_no',
            )
            ->orderBy('customers.customer_name','ASC')
            ->where('is_active',TRUE)
            ->where('is_deleted',FALSE)
            ;

            if($id!=null){ 
                $customers->where('customer_id',$id);
            }

        return $customers;
    }

}
