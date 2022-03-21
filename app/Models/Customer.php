<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
class Customer extends Authenticatable
{
    use HasFactory,HasApiTokens;

    protected $guarded=['id','created_at','updated_at'];

    protected $hidden=['password'];

    public function cart()
    {
    return  $this->hasMany(Cart::class);
    }
    public function product()
    {
        return $this->hasManyThrough(Product::class,Cart::class);
    }
}
