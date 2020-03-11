<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\References\Location;
use App\Models\Transactions\ContractInfo;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Auth;


class LocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$departments = Department::where('is_deleted', 0)->orderBy('department_id', 'asc')->paginate(5);
        $locations = Location::where('is_deleted', 0)->orderBy('location_id', 'desc')->get();
        return Reference::collection($locations);
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
                'location_code' => 'required',
                'location_desc' => 'required'
            ]
        )->validate();

        $location = new Location();
        $location->location_code = $request->input('location_code');
        $location->location_desc = $request->input('location_desc');
        $location->created_datetime = Carbon::now();
        $location->created_by = Auth::user()->id;
    
        $location->save();

        //return json based from the resource data
        return ( new Reference( $location ))
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
        $location = Location::findOrFail($id);

        return ( new Reference( $location ) )
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
        $location = Location::findOrFail($id);

        Validator::make($request->all(),
            [
                'location_code' => 'required',
                'location_desc' => 'required'
            ]
        )->validate();

        
        $location->location_code = $request->input('location_code');
        $location->location_desc = $request->input('location_desc');
        $location->modified_datetime = Carbon::now();
        $location->modified_by = Auth::user()->id;


        //update  based on the http json body that is sent
        $location->update();

        return ( new Reference( $location ) )
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
        $location = Location::findOrFail($id);
        $location->is_deleted = 1;
        $location->deleted_datetime = Carbon::now();
        $location->deleted_by = Auth::user()->id;

        //update classification based on the http json body that is sent
        $location->save();

        return ( new Reference( $location ) )
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

        if(ContractInfo::where('location_id', '=', $id)
            ->where('is_deleted', 0)
            ->exists()) {
            $exists = 'true';
        }
        
        return $exists;
    }
}
