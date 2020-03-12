<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\References\TaxType;
use App\Http\Resources\Reference;

class TaxTypesController extends Controller
{

    public function index()
    {
        $taxtypes = TaxType::select(
            'tax_types.*',
            )
            ->where('tax_types.is_deleted',FALSE)
            ;
        return Reference::collection($taxtypes->get())
        ->response()
        ->setStatusCode(200);
    }

}
