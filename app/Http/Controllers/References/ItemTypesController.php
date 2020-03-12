<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\References\ItemType;
use App\Http\Resources\Reference;

class ItemTypesController extends Controller
{

    public function index()
    {
        $itemtypes = ItemType::select(
            'item_types.*',
            )
            ->where('item_types.is_deleted',FALSE)
            ;
        return Reference::collection($itemtypes->get())
        ->response()
        ->setStatusCode(200);
    }

}
