<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\References\AccountClass;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
class AccountClassesController extends Controller
{

    public function index()
    {
        return Reference::collection($this->getAccountClasses()->get())
        ->response()
        ->setStatusCode(200);
    }

    public function create(Request $request)
    {
        Validator::make($request->all(),
            [
                'account_class' => 'required',
                'account_type_id' => 'required|not_in:0',
            ],
            [
                'not_in' => 'The :attribute field is required.'
            ])
            ->setAttributeNames([
                'account_class' => 'Classification Name',
                'account_type_id' => 'Account Type',
            ]
            )->validate();

        $accountclass = new AccountClass();
        $accountclass->account_class = $request->input('account_class');
        $accountclass->description = $request->input('description');
        $accountclass->account_type_id = $request->input('account_type_id');
        $accountclass->date_created = Carbon::now();
        $accountclass->save();

        return ( new Reference( $this->getAccountClasses($accountclass->account_class_id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function show($id)
    {
        return new Reference($this->getAccountClasses($id)->get()[0]);
    }

    public function update(Request $request,$id)
    {
        Validator::make($request->all(),
            [
                'account_class' => 'required',
                'account_type_id' => 'required|not_in:0',
            ],
            [
                'not_in' => 'The :attribute field is required.'
            ])
            ->setAttributeNames([
                'account_class' => 'Classification Name',
                'account_type_id' => 'Account Type',
            ]
            )->validate();
        $accountclass = AccountClass::findOrFail($id);
        $accountclass->account_class = $request->input('account_class');
        $accountclass->description = $request->input('description');
        $accountclass->account_type_id = $request->input('account_type_id');
        $accountclass->date_modified = Carbon::now();
        $accountclass->save();

        return ( new Reference( $this->getAccountClasses($id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function delete($id)
    {   
        $accountclass = AccountClass::findOrFail($id);
        $accountclass->is_deleted = 1;
        $accountclass->date_deleted = Carbon::now();
        $accountclass->save();

        return ( new Reference( $accountclass ) )
            ->response()
            ->setStatusCode(200);
    }

    public function checkIfUsed()
    {
        $exists = 'false';
        return $exists;
    }

    public function getAccountClasses($id=null)
    {
        $bank = AccountClass::select(
            'account_classes.account_class_id',
            'account_classes.account_class',
            'account_classes.description',
            'account_classes.account_type_id',
            'account_types.account_type'
            )
            ->leftJoin('account_types', 'account_types.account_type_id', '=', 'account_classes.account_type_id')
            ->where('account_classes.is_active',TRUE)
            ->where('account_classes.is_deleted',FALSE)
            ->orderBy('account_classes.account_class','asc')
            ;

            if($id!=null){ 
                $bank->where('account_class_id',$id);
            }

        return $bank;
    }
}
