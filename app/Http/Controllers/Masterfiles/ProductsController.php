<?php

namespace App\Http\Controllers\Masterfiles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Masterfiles\Product;
use App\Http\Resources\Reference;

class ProductsController extends Controller
{

    public function index()
    {
        return Reference::collection($this->GetProductListMinimal()->get())
        ->response()
        ->setStatusCode(200);
    }
    
    public function create(Request $request)
    {
        $product = new Product();
        $product->product_code = $request->input('product_code');
        $product->product_desc = $request->input('product_desc');
        $product->product_desc1 = $request->input('product_desc1');
        $product->brand_id = $request->input('brand_id');
        $product->category_id = $request->input('category_id');
        $product->tax_type_id = $request->input('tax_type_id');
        $product->is_bulk = $request->input('is_bulk');
        $product->parent_unit_id = $request->input('parent_unit_id');
        $product->child_unit_desc = $request->input('child_unit_desc');
        $product->child_unit_id = $request->input('child_unit_id');
        $product->item_type_id = $request->input('item_type_id');
        $product->purchase_cost = $request->input('purchase_cost');
        $product->sale_price = $request->input('sale_price');
        $product->discounted_price = $request->input('discounted_price');
        $product->dealer_price = $request->input('dealer_price');
        $product->distributor_price = $request->input('distributor_price');
        $product->product_warn = $request->input('product_warn');
        $product->product_ideal = $request->input('product_ideal');
        $product->income_account_id = $request->input('income_account_id');
        $product->expense_account_id = $request->input('expense_account_id');
        $product->save();

        return ( new Reference( $this->GetProductListMinimal($product->product_id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function show($id)
    {
        return ( new Reference($this->GetProductListMinimal($id)->get()[0]))
            ->response()
            ->setStatusCode(200);
        
    }

    public function update(Request $request,$id)
    {
        $product = Product::findOrFail($id);
        $product->product_code = $request->input('product_code');
        $product->product_desc = $request->input('product_desc');
        $product->product_desc1 = $request->input('product_desc1');
        $product->brand_id = $request->input('brand_id');
        $product->category_id = $request->input('category_id');
        $product->tax_type_id = $request->input('tax_type_id');
        $product->is_bulk = $request->input('is_bulk');
        $product->parent_unit_id = $request->input('parent_unit_id');
        $product->child_unit_desc = $request->input('child_unit_desc');
        $product->child_unit_id = $request->input('child_unit_id');
        $product->item_type_id = $request->input('item_type_id');
        $product->purchase_cost = $request->input('purchase_cost');
        $product->sale_price = $request->input('sale_price');
        $product->discounted_price = $request->input('discounted_price');
        $product->dealer_price = $request->input('dealer_price');
        $product->distributor_price = $request->input('distributor_price');
        $product->product_warn = $request->input('product_warn');
        $product->product_ideal = $request->input('product_ideal');
        $product->income_account_id = $request->input('income_account_id');
        $product->expense_account_id = $request->input('expense_account_id');
        $product->save();

        return ( new Reference( $this->GetProductListMinimal($id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function delete($id)
    {   
        $product = Product::findOrFail($id);
        $product->is_deleted = 1;
        $product->save();

        return ( new Reference( $product ) )
            ->response()
            ->setStatusCode(200);
    }

    public function checkIfUsed()
    {
        $exists = 'false';
        return $exists;
    }
    public function GetProductListMinimal($id=null) // List only, without on hand
    {
        $products = Product::select(
            'products.*',
            'categories.category_name',
            'units.unit_name as parent_unit_name',
            )
            ->leftJoin('categories', 'categories.category_id', '=', 'products.category_id')
            ->leftJoin('units', 'units.unit_id', '=', 'products.parent_unit_id')
            ->where('products.is_active',TRUE)
            ->where('products.is_deleted',FALSE)
            ;

            if($id!=null){ 
                $products->where('product_id',$id);
            }

        return $products;
    }

}
