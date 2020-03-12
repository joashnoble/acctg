<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\References\CustomerType;
use App\Http\Resources\Reference;

class CustomerTypesController extends Controller
{

    public function index()
    {
        $itemtypes = CustomerType::select(
            'customer_type.*',
            )
            ->where('customer_type.is_deleted',FALSE)
            ;
        return Reference::collection($itemtypes->get())
        ->response()
        ->setStatusCode(200);
    }

}
