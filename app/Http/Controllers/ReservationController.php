<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CLUB\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function reserve(Request $request){

        $user_id = Auth::id();
        $validate = Validator::make($request->all(), [

            'course_id' => 'required',
            'clas' => 'required',
            'number_of_people' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }

        $reserve = Reservation::create([
            'user_id' =>$user_id,
            'course_id' => $request-> course_id,
            'number_of_people' => $request -> number_of_people,
            'clas' => $request -> clas,
            'status' => 'pending'
        ]);

        $response = [
            'message' => 'reservation is successfully.',
            'course' => $reserve,
            'status' => true
        ];

        return response()->json($response);

    }
}
