<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CLUB\Clas;
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
        $capacity = Clas::where('course_id',$request->course_id)
            ->where('class','=',$request->clas)->first()-> capacity;

        $count = Clas::where('course_id',$request->course_id)
            ->where('class','=',$request->clas)->first()-> counter;
       $counter = $count + $request->number_of_people;

       if($counter>=$capacity){
           return $response = [
               'message' => 'you have passed the limit',
               'the allowed number is : ' => $capacity - $count,
               'status' => false
           ];
       }

        $reserve = Reservation::create([
            'user_id' =>$user_id,
            'course_id' => $request-> course_id,
            'number_of_people' => $request -> number_of_people,
            'clas' => $request -> clas,
            'status' => 'pending'
        ]);
        $clas = Clas::where('course_id',$request->course_id)
            ->where('class','=',$request->clas)->first();

        $clas -> increment('counter',$request->number_of_people);

        $capacity = Clas::where('course_id',$request->course_id)
            ->where('class','=',$request->clas)->first()-> capacity;

         $count = Clas::where('course_id',$request->course_id)
            ->where('class','=',$request->clas)->first()-> counter;

        if($count == $capacity) {

           Clas::where('course_id',$request->course_id)
                ->where('class',$request->clas)->update(['status' => 1]);
        }

        $response = [
            'message' => 'reservation is successfully.',
            'course' => $reserve,
            'status' => true
        ];

        return response()->json($response);

    }

    public function editReservation(Request $request,$Rid){

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


         $OLD_NUMBER_OF_PEOPLE = Reservation::where('id',$Rid)
            ->first()->number_of_people;

        $capacity = Clas::where('course_id',$request->course_id)
            ->where('class','=',$request->clas)->first()-> capacity;

        $count = Clas::where('course_id',$request->course_id)
            ->where('class','=',$request->clas)->first()-> counter;
          $counter = ($count-$OLD_NUMBER_OF_PEOPLE) + $request->number_of_people;

        if($counter>=$capacity){
            return $response = [
                'message' => 'you have passed the limit',
                'the allowed number is : ' => $capacity - $count,
                'status' => false
            ];
        }

     Reservation::where('id',$Rid)->update
     ([ 'number_of_people' => $request -> number_of_people]);

        $oldCounter = Clas::where('course_id',$request->course_id)
            ->where('class','=',$request->clas)->first()-> counter;

        Clas::where('course_id',$request->course_id)
            ->where('class','=',$request->clas)->update
        (['counter' => ($oldCounter-$OLD_NUMBER_OF_PEOPLE)
            +$request->number_of_people]);

        $capacity = Clas::where('course_id',$request->course_id)
            ->where('class','=',$request->clas)->first()-> capacity;

        $count = Clas::where('course_id',$request->course_id)
            ->where('class','=',$request->clas)->first()-> counter;

        if($count == $capacity) {

            Clas::where('course_id',$request->course_id)
                ->where('class',$request->clas)->update(['status' => 1]);
        }

        $response = [
            'message' => 'reservation is edited successfully.',
            'status' => true
        ];

        return response()->json($response);

    }

    public function UserReservations($user_id)
    {

        $reservations =Reservation::where('user_id', $user_id)->get();

        return response()->json([
            'reservations' => $reservations,
            'status' => true
        ]);
    }



    public function TrainerReservation($trainer_id)
    {
        $reservations = Reservation::whereHas('course.trainer', function($query) use ($trainer_id) {
            $query->where('id', $trainer_id);
        })->get();

        return response()->json([
            'reservations' => $reservations,
            'status' => true
        ]);
    }

    public function showSpecificReservation($id)
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json([
                'message' => 'Reservation not found!',
                'status' => false
            ]);
        }

        return response()->json([
            'reservation' => $reservation,
            'status' => true
        ]);
    }

}
