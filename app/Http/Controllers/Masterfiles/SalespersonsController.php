<?php

namespace App\Http\Controllers\Masterfiles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Masterfiles\Salesperson;
use App\Http\Resources\Reference;
use DB;
class SalespersonsController extends Controller
{

    public function index()
    {
        return Reference::collection($this->GetSalespersons()->get())
        ->response()
        ->setStatusCode(200);
    }
    
    public function create(Request $request)
    {
        $salesperson = new Salesperson();
        $salesperson->salesperson_code = $request->input('salesperson_code');
        $salesperson->firstname = $request->input('firstname');
        $salesperson->middlename = $request->input('middlename');
        $salesperson->lastname = $request->input('lastname');
        $salesperson->contact_no = $request->input('contact_no');
        $salesperson->department_id = $request->input('department_id');
        $salesperson->tin_no = $request->input('tin_no');
        $salesperson->save();

        return ( new Reference( $this->GetSalespersons($salesperson->salesperson_id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function show($id)
    {
        return ( new Reference($this->GetSalespersons($id)->get()[0]))
            ->response()
            ->setStatusCode(200);
        
    }

    public function update(Request $request,$id)
    {
        $salesperson = Salesperson::findOrFail($id);
        $salesperson->salesperson_code = $request->input('salesperson_code');
        $salesperson->firstname = $request->input('firstname');
        $salesperson->middlename = $request->input('middlename');
        $salesperson->lastname = $request->input('lastname');
        $salesperson->contact_no = $request->input('contact_no');
        $salesperson->department_id = $request->input('department_id');
        $salesperson->tin_no = $request->input('tin_no');
        $salesperson->save();

        return ( new Reference( $this->GetSalespersons($id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function delete($id)
    {   
        $salesperson = Salesperson::findOrFail($id);
        $salesperson->is_deleted = 1;
        $salesperson->save();

        return ( new Reference( $salesperson ) )
            ->response()
            ->setStatusCode(200);
    }

    public function checkIfUsed()
    {
        $exists = 'false';
        return $exists;
    }

    public function GetSalespersons($id=null) // List only, without on hand
    {
        $customers = Salesperson::select(
            'salesperson_id',
            'salesperson_code',
            'firstname',
            'middlename',
            'lastname',
            'contact_no',
            'department_id',
            DB::raw("CONCAT(COALESCE(firstname,''), ' ', COALESCE(middlename,''), ' ', COALESCE(lastname,'')) as full_name"),
            'tin_no',
            )
            ->where('is_active',TRUE)
            ->where('is_deleted',FALSE)
            ;

            if($id!=null){ 
                $customers->where('salesperson_id',$id);
            }

        return $customers;
    }

}
