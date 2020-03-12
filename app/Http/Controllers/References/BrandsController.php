<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\References\Brand;
use App\Http\Resources\Reference;
use Illuminate\Support\Facades\Validator;
class BrandsController extends Controller
{

    public function index()
    {
        $brands = Brand::select(
            'brand_id',
            'brand_name'
            )
            ->where('is_active',TRUE)
            ->where('is_deleted',FALSE)
            ->orderBy('brand_name','asc')
            ;
        return Reference::collection($brands->get())            
        ->response()
        ->setStatusCode(200);
    }

    public function create(Request $request)
    {
        Validator::make($request->all(),
        [
            'brand_name' => 'required'
        ])->validate();
        $brand = new Brand();
        $brand->brand_name = $request->input('brand_name');
        $brand->save();

        return ( new Reference( $brand ))
                ->response()
                ->setStatusCode(201);
    }

    public function show($id)
    {
        $brand = Brand::findOrFail($id);
        return ( new Reference($brand) )
        ->response()
        ->setStatusCode(200);
    }

    public function update(Request $request,$id)
    {
        Validator::make($request->all(),
        [
            'brand_name' => 'required'
        ])->validate();
        $brand = Brand::findOrFail($id);
        $brand->brand_name = $request->input('brand_name');
        $brand->save();

        return ( new Reference( $brand ))
                ->response()
                ->setStatusCode(201);
    }

    public function delete($id)
    {   
        $brand = Brand::findOrFail($id);
        $brand->is_deleted = 1;
        $brand->save();

        return ( new Reference( $brand ) )
            ->response()
            ->setStatusCode(200);
    }

    public function checkIfUsed()
    {
        $exists = 'false';
        return $exists;
    }
}
