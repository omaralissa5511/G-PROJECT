<?php

namespace App\Http\Controllers;



use App\Models\User;
use http\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TESTcontroller extends Controller
{

//
//    public function __construct()
//    {
//
//        $this->middleware(['role_or_permission:manager|create-product']);
//       // $this->middleware('permission:create-product', ['only' => ['create','store']]);
//
//    }


   public function test (){

       return response()->json(['message' => 'hi boys']);
   }

    public function store (){

        return response()->json(['message' => 'hi boys']);
    }



}
