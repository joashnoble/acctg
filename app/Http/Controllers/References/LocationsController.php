<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\References\Location;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class LocationsController extends Controller
{

    public function index()
    {
        $location = Location::select(
            'location_id',
            'location_name'
            )
            ->where('locations.is_active',TRUE)
            ->where('locations.is_deleted',FALSE)
            ->orderBy('location_name','asc')
            ;
        return Reference::collection($location->get())
        ->response()
        ->setStatusCode(200);
    }

    public function create(Request $request)
    {
        Validator::make($request->all(),
        [
            'location_name' => 'required'
        ])->validate();
        $location = new Location();
        $location->location_name = $request->input('location_name');
        $location->save();

        return ( new Reference( $location ))
                ->response()
                ->setStatusCode(201);
    }

    public function show($id)
    {
        $location = Location::findOrFail($id);
        return ( new Reference($location) )
        ->response()
        ->setStatusCode(200);
    }

    public function update(Request $request,$id)
    {
        Validator::make($request->all(),
        [
            'location_name' => 'required'
        ])->validate();
        $location = Location::findOrFail($id);
        $location->location_name = $request->input('location_name');
        $location->save();

        return ( new Reference( $location ))
                ->response()
                ->setStatusCode(201);
    }

    public function delete($id)
    {   
        $location = Location::findOrFail($id);
        $location->is_deleted = 1;
        $location->save();

        return ( new Reference( $location ) )
            ->response()
            ->setStatusCode(200);
    }

    public function checkIfUsed()
    {
        $exists = 'false';
        return $exists;
    }

}
