<?php

namespace App\Http\Controllers;

use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    public function create(Request $request){
        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'phone' => 'required',
            'message' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }

        $support=Support::create([
            'email'=>$request->email,
            'phone_number'=>$request->phone,
            'message'=>$request->message
        ]);

        return response()->json([
            'support'=>$support,
            'status'=>true
        ]);
    }

    public function getAllSupportNotReply(){
        $supports=Support::where('reply',0)->get();

        return response()->json([
            'supports'=>$supports,
            'status'=>true
        ]);
    }

    public function reply($id){

        $find=Support::find($id);
        if(!$find)
            return response()->json([
                'message'=>'support not found',
                'status'=>false
            ]);

        $reply=Support::where('id',$id)->update([
           'reply'=>1
        ]);

        return response()->json([
            'message'=>'updated',
            'status'=>true
        ]);
    }

}
