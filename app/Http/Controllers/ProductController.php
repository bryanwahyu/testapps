<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $product=new Product();
        if($req->has('orderBy')){
            $product=$product->OrderBy($req->orderBy,$req->orderType);
        }
        if($req->has('with_count')){
            $with_count=explode(',',$req->with_count);
            $product=$product->withCount($with_count);
        }
        if($req->has('page_size')){
           $product= $product->pagination($req->page_size);
        }else{
           $product=$product->get();
        }
        $json['data']=$product;
        $json['code']=200;
        $json['message']="list of Product";

        return $this->send_json($json);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        $req->validate([
            'name'=>'required',
            'description'=>'required',
            'price'=>'required'
        ]);
        DB::beginTransaction();
        try {
            $product=Product::create($req->all());

            $json['data']=$product;
            $json['code']=200;
            $json['message']="Product has been created";
            DB::commit();
            return $this->send_json($json);
        } catch (\Exception $e) {
            DB::rollBack();
            $json['data']=null;
            $json['code']=500;
            $json['message']=$e->getMessage();

            return  $this->send_json($json);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product,Request $req)
    {
        if($req->has('with')){
            $with=explode(',',$req->with);
            $product=$product->load($with);
        }
        $json['data']=$product;
        $json['code']=200;
        $json['message']="detail of product";

        return $this->send_json($json);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, Product $product)
    {
        $req->validate([
            'name'=>'required',
            'description'=>'required',
            'price'=>'required'
        ]);
        DB::beginTransaction();
        try {
            $product->update($req->all());
            $json['data']=$product;
            $json['code']=200;
            $json['message']="Product has updated";
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $json['data']=null;
            $json['code']=500;
            $json['message']=$e->getMessage();
        }
        return $this->send_json($json);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if($product->has('cart')){
            $product->cart()->delete();
        }
        $product->delete();

        $json['code']=200;
        $json['data']=null;
        $json['message']="success delete product";

        return $this->send_json($json);
    }
}
