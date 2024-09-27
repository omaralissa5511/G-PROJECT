<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $token=DeviceToken::where('user_id',$request->user_id)->where('token',$request->token)->exists();
        if(!$token){
            DeviceToken::create([
                'user_id'=>$request->user_id,
                'token'=>$request->token
            ]);

            return response()->json([
                'message'=>'Token is stored',
                'status'=>true
            ]);
        }

        return response()->json([
            'message'=>'Token is found',
            'status'=>false
        ]);
    }
}
