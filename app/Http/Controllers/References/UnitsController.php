<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\References\Unit;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class UnitsController extends Controller
{

    public function index()
    {
        $units = Unit::select(
            'unit_id',
            'unit_name',
            'unit_desc',
            )
            ->where('is_active',TRUE)
            ->where('is_deleted',FALSE)
            ->orderBy('unit_name','asc')
            ;
        return Reference::collection($units->get())            
        ->response()
        ->setStatusCode(200);
    }

    public function create(Request $request)
    {
        Validator::make($request->all(),
        [
            'unit_name' => 'required'
        ])->validate();

        $unit = new Unit();
        $unit->unit_name = $request->input('unit_name');
        $unit->unit_desc = $request->input('unit_desc');
        $unit->date_created = Carbon::now();
        $unit->save();

        return ( new Reference( $unit ))
                ->response()
                ->setStatusCode(201);
    }

    public function show($id)
    {
        $unit = Unit::findOrFail($id);
        return (new Reference($unit))
        ->response()
        ->setStatusCode(200);
    }

    public function update(Request $request,$id)
    {
        Validator::make($request->all(),
        [
            'unit_name' => 'required'
        ])->validate();
        $unit = Unit::findOrFail($id);
        $unit->unit_name = $request->input('unit_name');
        $unit->unit_desc = $request->input('unit_desc');
        $unit->date_modified = Carbon::now();
        $unit->save();

        return ( new Reference( $unit ))
                ->response()
                ->setStatusCode(201);
    }

    public function delete($id)
    {   
        $unit = Unit::findOrFail($id);
        $unit->is_deleted = 1;
        $unit->save();

        return ( new Reference( $unit ) )
            ->response()
            ->setStatusCode(200);
    }

    public function checkIfUsed()
    {
        $exists = 'false';
        return $exists;
    }

}
