<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Product;
class ShoppingCartController extends Controller
{
    public function add_into_cart(Cart $cart,Request $req)
    {
        $req->validate([
            'product_id'=>"required",
            "quantity"=>'required'
        ]);
        DB::beginTransaction();
        $product=Product::find($req->product_id);
        try {
        $cart=Cart::where('customer_id',Auth::user()->id)->where('product_id',$req->product_id)->first();
        if($cart){
            $cart->quantity=$req->quantity;
            $cart->total=$cart->product->price*$req->quantity;
            $cart->save();
        }else{
            $cart=new Cart();
            $cart->customer_id=Auth::user()->id;
            $cart->product_id=$product->id;
            $cart->quantity=$req->quantity;
            $cart->total=$req->quantity*$product->price;
            $cart->save();
        }
            $json['code']=200;
            $json['data']=$cart;
            $json['message']="Cart has been updated or Added";
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $json['code']=500;
            $json['data']=null;
            $json['message']=$e->getMessage();
        }

        return $this->send_json($json);
    }
    public function auth_index_cart(Request $req)
    {
        $cart=Cart::where('customer_id',Auth::user()->id);
        if($req->has('orderBy')){

           $cart=$cart->OrderBy($req->orderBy,$req->orderType);
        }
        if($req->has('page_size')){
            $cart=$cart->paginate($req->page_size);
        }else{
            $cart=$cart->get();
            if($req->has('take')){
               $cart=$cart->take($req->take);
            }
        }
        $json['code']=200;
        $json['data']=$cart;
        $json['message']='List of Cart';
        $json['total_price']=$cart->sum("total");
        return $this->send_json($json);
    }
    public function delete_cart(Cart $cart)
    {
        $cart->delete();

        $json['code']=200;
        $json['data']=null;
        $json['message']="success to delete cart";

        return $this->send_json($json);
    }
    public function pay_cart()
    {
        $cart=Cart::where('customer_id',Auth::user()->id);

        $sum=$cart->sum('total');
        // payment gateway call and store to transcation


        $json['message']="Pembayaran berhasil seharga ".$sum;
        $json['code']=200;
        $json['data']=$cart->get();

        $cart->delete();

        return $this->send_json($json);

    }
}
