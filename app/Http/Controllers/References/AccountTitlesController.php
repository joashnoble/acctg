<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\References\AccountTitle;
use App\Models\References\AccountClass;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
class AccounttitlesController extends Controller
{

    public function index()
    {
        return Reference::collection($this->getComprehensiveAccountTitles()->get())
        ->response()
        ->setStatusCode(200);
    }

    public function create(Request $request)
    {
        Validator::make($request->all(),
        [
            'account_no' => 'required',
            'account_title' => 'required'
        ])->validate();
        $parent_account_id = $request->input('parent_account_id');
        $accounttitle = new AccountTitle();
        $accounttitle->account_no = $request->input('account_no');
        $accounttitle->account_title = $request->input('account_title');
        $accounttitle->account_class_id = $request->input('account_class_id');
        $accounttitle->parent_account_id = $parent_account_id;
        $accounttitle->grand_parent_id = $request->input('grand_parent_id');
        $accounttitle->description = $request->input('description');
        $accounttitle->save();

        $account_id = $accounttitle->account_id;
        $modifyaccounttitle = AccountTitle::findOrFail($account_id);
            if($parent_account_id>0){
                $data = AccountTitle::findOrFail($parent_account_id);
                $modifyaccounttitle->grand_parent_id = $data['grand_parent_id'];
                $modifyaccounttitle->account_class_id = $data['account_class_id'];
            }else{
                $modifyaccounttitle->grand_parent_id = $account_id;
            }
        $modifyaccounttitle->save();
        return ( new Reference( $this->getComprehensiveAccountTitles($accounttitle->account_id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function show($id)
    {
        return ( new Reference($this->getComprehensiveAccountTitles($id)->get()[0]))
            ->response()
            ->setStatusCode(200);
        
    }

    public function update(Request $request,$id)
    {
        Validator::make($request->all(),
        [
            'account_no' => 'required',
            'account_title' => 'required'
        ])->validate();
        $parent_account_id = $request->input('parent_account_id');
        $account_id = $id;
        $accounttitle = AccountTitle::findOrFail($account_id);
        $accounttitle->account_no = $request->input('account_no');
        $accounttitle->account_title = $request->input('account_title');
        $accounttitle->account_class_id = $request->input('account_class_id');
        $accounttitle->parent_account_id = $parent_account_id;
        $accounttitle->grand_parent_id = $request->input('grand_parent_id');
        $accounttitle->description = $request->input('description');
        $accounttitle->save();

        $modifyaccounttitle = AccountTitle::findOrFail($account_id);
            if($parent_account_id>0){
                $data = AccountTitle::findOrFail($parent_account_id);
                $modifyaccounttitle->grand_parent_id = $data['grand_parent_id'];
                $modifyaccounttitle->account_class_id = $data['account_class_id'];
            }else{
                $modifyaccounttitle->grand_parent_id = $account_id;
            }
        $modifyaccounttitle->save();
        return ( new Reference( $this->getComprehensiveAccountTitles($accounttitle->account_id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function delete($id)
    {   
        $accounttitle = AccountTitle::findOrFail($id);
        $accounttitle->is_deleted = 1;
        $accounttitle->save();

        return ( new Reference( $accounttitle ) )
            ->response()
            ->setStatusCode(200);
    }

    public function checkIfUsed()
    {
        $exists = 'false';
        return $exists;
    }


    public function getComprehensiveAccountTitles($id=null) 
    // GET ACCOUNT ID AND TITLE ONLY FOR FAST LOADING
    {
        $accounttitles = AccountTitle::select(
            'account_titles.account_id',
            'account_titles.account_no',
            'account_titles.account_title',
            'account_titles.account_class_id',
            'account_titles.parent_account_id',
            'account_titles.grand_parent_id',
            'account_titles.description',
            'account_classes.account_class',
            'account_types.account_type',
            'parent.account_title as parent_account',
            )
            ->leftJoin('account_classes', 'account_classes.account_class_id', '=', 'account_titles.account_class_id')
            ->leftJoin('account_types', 'account_types.account_type_id', '=', 'account_classes.account_type_id')
            ->leftJoin('account_titles as parent', 'parent.account_id', '=', 'account_titles.parent_account_id')
            ->where('account_titles.is_active',TRUE)
            ->where('account_titles.is_deleted',FALSE)
            ->orderBy('account_titles.account_title','asc')
            ;
            if($id!=null){ 
                $accounttitles->where('account_titles.account_id',$id);
            }
        return $accounttitles;
    }

    public function options() // USED IN SELECT2 OPTIONS
    {
        $accounttitlesoptions = AccountTitle::select(
            'account_id',
            'account_title',
            )
            ->where('is_active',TRUE)
            ->where('is_deleted',FALSE)
            ->orderBy('account_title','asc');

        return Reference::collection($accounttitlesoptions->get())
        ->response()
        ->setStatusCode(200);
    }

    public function tree() // USED IN SELECT2 OPTIONS
    {

        $accounts['types'] =AccountClass::select(
            'account_classes.account_class_id',
            'account_classes.account_class')
            ->where('account_classes.is_active',TRUE)
            ->where('account_classes.is_deleted',FALSE)->get()
            ;
            
        $accounts['accounts'] = AccountTitle::select(
            'account_id',
            'account_class_id',
            'account_title',
            'parent_account_id',
            )
            ->where('is_active',TRUE)
            ->where('parent_account_id','=','0')
            ->where('is_deleted',FALSE)->get();

        $accounts['child'] = AccountTitle::select(
            'account_id',
            'account_title',
            'parent_account_id',
            )
            ->where('is_active',TRUE)
            ->where('is_deleted',FALSE)
            ->where('parent_account_id','!=','0')
            ->orderBy('account_title','asc')->get();



        return $accounts;
    }
}