<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\References\AccountTitle;
use App\Models\Settings\GeneralConfiguration;
use App\Models\Settings\StatementOfAccountSetting;
use App\Models\Settings\AssetSetting;
use App\Models\Settings\CompanySetting;
use App\Http\Resources\Reference;
use DB;

class GeneralConfigurationController extends Controller
{

    public function index()
    {
        $accounts['current'] =GeneralConfiguration::where('integration_id',1)->get()[0];
        return $accounts;
    }

    public function optionsandselects()
    {
        $accounts['accounttitles'] = AccountTitle::select(
            'account_id',
            'account_title',
            )
            ->where('is_active',TRUE)
            ->where('is_deleted',FALSE)
            ->orderBy('account_title','asc')->get();

        $accounts['soaacccounttitles'] = AccountTitle::select(
            'account_titles.account_id',
            'account_titles.account_title',
            DB::raw('IF(ISNULL(soa_settings.soa_account_id),"0","1") as checked'),
            )
            ->leftJoin('soa_settings', 'soa_settings.soa_account_id', '=', 'account_titles.account_id')
            ->leftJoin('account_classes', 'account_classes.account_class_id', '=', 'account_titles.account_class_id')
            ->where('account_titles.is_active',TRUE)
            ->where('account_titles.is_deleted',FALSE)
            ->where('account_classes.account_type_id', "1")
            ->orderBy('account_title','asc')->get();

            $accounts['faacccounttitles'] = AccountTitle::select(
                'account_titles.account_id',
                'account_titles.account_title',
                DB::raw('IF(ISNULL(asset_settings.asset_account_id),"0","1") as checked'),
                )
                ->leftJoin('asset_settings', 'asset_settings.asset_account_id', '=', 'account_titles.account_id')
                ->leftJoin('account_classes', 'account_classes.account_class_id', '=', 'account_titles.account_class_id')
                ->where('account_titles.is_active',TRUE)
                ->where('account_titles.is_deleted',FALSE)
                ->where('account_classes.account_type_id', "1")
                ->orderBy('account_title','asc')->get();

        return $accounts;
    }
    public function customers(request $request)
    {
        $genconf=GeneralConfiguration::findOrFail(1);
        $genconf->output_tax_account_id = $request->input('output_tax_account_id');
        $genconf->receivable_account_id = $request->input('receivable_account_id');
        $genconf->receivable_discount_account_id = $request->input('receivable_discount_account_id');
        $genconf->payment_from_customer_id = $request->input('payment_from_customer_id');
        $genconf->save();
        return ( new Reference( $genconf ))
                ->response()
                ->setStatusCode(201);
    }

    public function suppliers(request $request)
    {
        $genconf=GeneralConfiguration::findOrFail(1);
        $genconf->input_tax_account_id = $request->input('input_tax_account_id');
        $genconf->payable_account_id = $request->input('payable_account_id');
        $genconf->payable_discount_account_id = $request->input('payable_discount_account_id');
        $genconf->payment_to_supplier_id = $request->input('payment_to_supplier_id');
        $genconf->supplier_wtax_account_id = $request->input('supplier_wtax_account_id');
        $genconf->save();
        return ( new Reference( $genconf ))
                ->response()
                ->setStatusCode(201);
    }

    public function others(request $request)
    {
        $genconf=GeneralConfiguration::findOrFail(1);
        $genconf->retained_earnings_id = $request->input('retained_earnings_id');
        $genconf->petty_cash_account_id = $request->input('petty_cash_account_id');
        $genconf->depreciation_expense_debit_id = $request->input('depreciation_expense_debit_id');
        $genconf->depreciation_expense_credit_id = $request->input('depreciation_expense_credit_id');
        $genconf->save();
        return ( new Reference( $genconf ))
                ->response()
                ->setStatusCode(201);
    }

    public function inventory(request $request)
    {
        $genconf=GeneralConfiguration::findOrFail(1);
        $genconf->sales_invoice_inventory = (int)$request->input('sales_invoice_inventory');
        $genconf->cash_invoice_inventory = (int)$request->input('cash_invoice_inventory');
        $genconf->dispatching_invoice_inventory = (int)$request->input('dispatching_invoice_inventory');
        $genconf->save();
        return ( new Reference( $genconf ))
                ->response()
                ->setStatusCode(201);
    }

    public function statementofaccount(request $request)
    {
        DB::table('soa_settings')->truncate();
        $items =  $request->get('selectedItems');
        foreach($items as $item){
            $statementofaccounts = new StatementOfAccountSetting;
            $statementofaccounts->soa_account_id = $item['account_id'];
            $statementofaccounts->save();
        }

        return ( new Reference( $items ))
                ->response()
                ->setStatusCode(201);
    }

    public function fixedassets(request $request)
    {
        DB::table('asset_settings')->truncate();
        $items =  $request->get('selectedItems');
        foreach($items as $item){
            $statementofaccounts = new AssetSetting;
            $statementofaccounts->asset_account_id = $item['account_id'];
            $statementofaccounts->save();
        }

        return ( new Reference( $items ))
                ->response()
                ->setStatusCode(201);
    }
    public function companyinfo()
    {
        $accounts['companyinfo'] =CompanySetting::where('company_id',1)->get()[0];
        return $accounts;
    }

    public function companyinfoset(request $request)
    {
        $genconf=CompanySetting::findOrFail(1);
        $genconf->company_name = $request->input('company_name');
        $genconf->company_address = $request->input('company_address');
        $genconf->email_address = $request->input('email_address');
        $genconf->mobile_no = $request->input('mobile_no');
        $genconf->landline = $request->input('landline');
        $genconf->business_type = $request->input('business_type');
        $genconf->logo_path = $request->input('logo_path');
        $genconf->tax_type_id = $request->input('tax_type_id');
        $genconf->save();
        return ( new Reference( $genconf ))
                ->response()
                ->setStatusCode(201);
    
    }

    public function birinfoset(request $request)
    {
        $genconf=CompanySetting::findOrFail(1);
        $genconf->registered_to = $request->input('registered_to');
        $genconf->registered_address = $request->input('registered_address');
        $genconf->zip_code = $request->input('zip_code');
        $genconf->telephone_no = $request->input('telephone_no');
        $genconf->tin_no = $request->input('tin_no');
        $genconf->rdo_no = $request->input('rdo_no');
        $genconf->nature_of_business = $request->input('nature_of_business');
        $genconf->industry_classification = $request->input('industry_classification');

        $genconf->save();
        return ( new Reference( $genconf ))
                ->response()
                ->setStatusCode(201);
    
    }
}
