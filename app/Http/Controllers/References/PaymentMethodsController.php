<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\References\PaymentMethod;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
class PaymentMethodsController extends Controller
{

    public function index()
    {
        $payments = PaymentMethod::select(
            'payment_method_id',
            'payment_method')
            ->where('is_active',TRUE)
            ->where('is_deleted',FALSE)
            ;
        return Reference::collection($payments->get())
        ->response()
        ->setStatusCode(200);
    }

  

}
