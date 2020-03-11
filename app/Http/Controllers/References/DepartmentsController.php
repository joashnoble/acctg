<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\References\Department;
use App\Models\Transactions\ContractInfo;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Auth;

class DepartmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$departments = Department::where('is_deleted', 0)->orderBy('department_id', 'asc')->paginate(5);
        $departments = Department::where('is_deleted', 0)->orderBy('department_id', 'desc')->get();
        return Reference::collection($departments);
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
                'department_code' => 'required',
                'department_desc' => 'required',
                'department_address' => 'required',
                'signatory_1' => 'required',
                'signatory_1_position' => 'required',
                'signatory_2' => 'required',
                'signatory_2_position' => 'required',
                'signatory_3' => 'required',
                'signatory_3_position' => 'required',
                'start_day' => 'required',
                'end_day' => 'required',
                'is_same' => 'required',
                'cut_off_day' => 'required'
            ]
        )->validate();

        $department = new Department();
        $department->department_code = $request->input('department_code');
        $department->department_desc = $request->input('department_desc');
        $department->department_address = $request->input('department_address');
        $department->signatory_1 = $request->input('signatory_1');
        $department->signatory_1_position = $request->input('signatory_1_position');
        $department->signatory_2 = $request->input('signatory_2');
        $department->signatory_2_position = $request->input('signatory_2_position');
        $department->signatory_3 = $request->input('signatory_3');
        $department->signatory_3_position = $request->input('signatory_3_position');
        $department->start_day = $request->input('start_day');
        $department->end_day = $request->input('end_day');
        $department->is_same = $request->input('is_same');
        $department->cut_off_day = $request->input('cut_off_day');
        $department->created_datetime = Carbon::now();
        $department->created_by = Auth::user()->id;
    
        $department->save();

        //return json based from the resource data
        return ( new Reference( $department ))
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
        $department = Department::findOrFail($id);

        return ( new Reference( $department ) )
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
        $department = Department::findOrFail($id);

        Validator::make($request->all(),
            [
                'department_code' => 'required',
                'department_desc' => 'required',
                'department_address' => 'required',
                'signatory_1' => 'required',
                'signatory_1_position' => 'required',
                'signatory_2' => 'required',
                'signatory_2_position' => 'required',
                'signatory_3' => 'required',
                'signatory_3_position' => 'required',
                'start_day' => 'required',
                'end_day' => 'required',
                'is_same' => 'required',
                'cut_off_day' => 'required'
            ]
        )->validate();

        
        $department->department_code = $request->input('department_code');
        $department->department_desc = $request->input('department_desc');
        $department->department_address = $request->input('department_address');
        $department->signatory_1 = $request->input('signatory_1');
        $department->signatory_1_position = $request->input('signatory_1_position');
        $department->signatory_2 = $request->input('signatory_2');
        $department->signatory_2_position = $request->input('signatory_2_position');
        $department->signatory_3 = $request->input('signatory_3');
        $department->signatory_3_position = $request->input('signatory_3_position');
        $department->start_day = $request->input('start_day');
        $department->end_day = $request->input('end_day');
        $department->is_same = $request->input('is_same');
        $department->cut_off_day = $request->input('cut_off_day');
        $department->modified_datetime = Carbon::now();
        $department->modified_by = Auth::user()->id;


        //update  based on the http json body that is sent
        $department->update();

        return ( new Reference( $department ) )
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
        $department = Department::findOrFail($id);

        $department->is_deleted = 1;
        $department->deleted_datetime = Carbon::now();
        $department->deleted_by = Auth::user()->id;

        //update classification based on the http json body that is sent
        $department->save();

        return ( new Reference( $department ) )
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

        if(ContractInfo::where('department_id', '=', $id)
            ->where('is_deleted', 0)
            ->exists()) {
            $exists = 'true';
        }
        return $exists;
    }
}
