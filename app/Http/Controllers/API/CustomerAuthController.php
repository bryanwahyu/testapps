<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
    public function register(Request $req)
    {
        $req->validate([
            'email'=>['required','unique:customers',],
            'password'=>'required',
            'name'=>'required'
        ]);

        try {
            $request=$req->all();
            $request['password']=bcrypt($req->password);

            $customer=Customer::create($request);

            $json['code']=200;
            $json['data']=$customer;
            $json['message']="Register successfully";

            return $this->send_json($json);
        } catch (\Exception $e) {
            $json['code']=400;
            $json['data']=null;
            $json['message']=$e->getMessage();

            return $this->send_json($json);
        }
    }
    public function login(Request $req)
    {
        $req->validate([
            'email'=>'required',
            'password'=>'required'
        ]);

        if(Auth::guard('customer-web')->attempt(['email'=>$req->email,'password'=>$req->password])){
            $json['data']=Auth::guard('customer-web')->user();
            $json['token']=Auth::guard('customer-web')->user()->CreateToken('customer')->accessToken;
            $json['message']="welcome";
            $json['code']=200;
        }
        else{
            $json['message']="email and password are wrong";
            $json['code']=401;
        }
        return $this->send_json($json);
    }
    public function change_password_itself(Request $req)
    {
        $req->validate([
            'old_password'=>'required',
            'new_password'=>'required'
        ]);

        if(Hash::check($req->old_password,Auth::user()->password)){
            Auth::user()->password=bcrypt($req->new_password);
            Auth::user()->save();

            $json['code']=200;
            $json['message']="your password has been changed";
        }
        else{
            $json['code']=201;
            $json['message']="Wrong password please contact you Adminstrator";
        }

        return $this->send_json($json);
    }
    public function auth_data(Request $req)
    {
        $user=Auth::user();
        if($req->has('with')){
            $with=explode(',',$req->with);
            $user->load($with);
        }
        $json['data']=$user;
        $json['code']=200;
        $json['message']="Authorization user";


        return $this->send_json($json);
    }
    public function change_auth_data(Request $req)
    {
        $user=Auth::user();

        $user->update($req->all());

        $json['data']=$user;
        $json['code']=200;
        $json['message']="User has been updated";
        return $this->send_json($user);

    }

}
