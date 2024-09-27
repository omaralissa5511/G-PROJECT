<?php

namespace App\Http\Controllers;

use App\Events\NotificationE;
use App\Events\Users;
use App\Http\Controllers\Controller;
use App\Models\CLUB\Booking;
use App\Models\CLUB\Clas;
use App\Models\CLUB\Course;
use App\Models\CLUB\Equestrian_club;
use App\Models\CLUB\Reservation;
use App\Models\Profile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Validator;
use function Laravel\Prompts\form;

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

        $price = Clas::where('class',$request->clas)->first()->price;
        $price = $price*$request->number_of_people;
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
            'price' =>$price,
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
        $message2 = 'Clas Updated';
        Broadcast(new \App\Events\Clas($message2));

        $response = [
            'message' => 'reservation is successfully.',
            'course' => $reserve,
            'status' => true
        ];

        $user_name = Profile::where('user_id',$user_id)->first()->FName;
        $course_description = Course::where('id',$request->course_id)
            ->first()->description;
        $course_price = Course::where('id',$request->course_id)
            ->first()->price;

            $message['message'] = 'reservation is done successfully.';
            $message['user_name'] = $user_name;
            $message['course_description'] = $course_description;
            $message['number_of_people'] = $reserve->number_of_people;
            $message['class'] = $reserve->clas;
            $message['status'] = $reserve->status;

        // notification
        $club_id = Course::where('id', $request->course_id)->first('club_id');
        $club_name = Equestrian_club::where('id',$club_id)->pluck('name')->first();
        $user1 = User::where('id',$user_id)->first();
        $notificationService = new \App\Services\Api\NotificationService();
        $notificationService->send($user1, 'Reservation is done successfully.', 'You have reserved '.$clas->class.' class of course '.$course_description.' at '.$club_name);
        // end notification
        broadcast(new NotificationE($user_id, $message));

        $message1 = 'new Reservation have added successfully.';
        Broadcast(new \App\Events\Reservation($message1));

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

     $reserve = Reservation::where('id',$Rid)->update
     ([ 'number_of_people' => $request -> number_of_people]);
        $message1 = 'Reservation updated';
        Broadcast(new \App\Events\Reservation($message1));

        $oldCounter = Clas::where('course_id',$request->course_id)
            ->where('class','=',$request->clas)->first()-> counter;

        Clas::where('course_id',$request->course_id)
            ->where('class','=',$request->clas)->update
        (['counter' => ($oldCounter-$OLD_NUMBER_OF_PEOPLE)
            +$request->number_of_people]);

        $message2 = 'Clas Updated';
        Broadcast(new \App\Events\Clas($message2));


        $capacity = Clas::where('course_id',$request->course_id)
            ->where('class','=',$request->clas)->first()-> capacity;

        $count = Clas::where('course_id',$request->course_id)
            ->where('class','=',$request->clas)->first()-> counter;

        if($count == $capacity) {

            Clas::where('course_id',$request->course_id)
                ->where('class',$request->clas)->update(['status' => 1]);


            $message3 = 'Clas Updated';
            Broadcast(new \App\Events\Clas($message3));

        }

        $booking = Reservation::where('id',$Rid)->first();
        $response = [
            'message' => 'reservation is edited successfully.',
            'booking' => $booking,
            'status' => true
        ];


        $user_name = Profile::where('id',$user_id)->first()->FName;

        $message['message'] = 'reservation is edited successfully.';
        $message['user_name'] = $user_name;
        $message['number_of_people'] = $request -> number_of_people;


        broadcast(new NotificationE($user_id, $message));

        return response()->json($response);

    }
            public function cancelReservation($reservationId)
            {
                    $user_id = Auth::id();
                    $reservation = Reservation::find($reservationId);
                    $courseStartDate = Course::where('id', $reservation->course_id)->first()->begin;
                    $courseStartDate = Carbon::parse($courseStartDate);
                    $now = Carbon::now();

                    if ($courseStartDate->diffInDays($now) <= 1) {
                        return response()->json([
                            'message' => 'لا يمكن إلغاء الحجز قبل بدء الدورة بيوم أو أقل.',
                            'status' => false
                        ]);
                    }

                    $numberOFpeople = $reservation->number_of_people;
                    $clas = Clas::where('course_id', $reservation->course_id)
                        ->where('class', '=', $reservation->clas)->first();
                    $newCounter = $clas->counter-$numberOFpeople;

                    $clas->update(['counter' => $newCounter]);
                    $clas->save();
                    $reservation->delete();


                $message2 = 'Clas Updated';
                Broadcast(new \App\Events\Clas($message2));

                $message3 = 'Reservation deleted';
                Broadcast(new \App\Events\Reservation($message3));

                $user_name = Profile::where('user_id',$user_id)->first()->FName;

                $message['message'] = 'تم إلغاء الحجز بنجاح.';
                $message['user_name'] = $user_name;



                broadcast(new NotificationE($user_id, $message));

                return response()->json([
                        'message' => 'تم إلغاء الحجز بنجاح.',
                        'status' => true,
                        'class' => $clas
                    ]);

            }

    public function Reserved_User_clubs()
    {
        $user_id = Auth::id();
        $reservations =Reservation::where('user_id', $user_id)->get('course_id');
        $ids = collect($reservations)->unique()->values()->all();
        if(!$ids){
            return response()->json([
                'message' => 'no clubs for you',
                'status' => false
            ]);
        }
        foreach ($ids as $id){
            $Cid[] = $id->course_id;
        }

        foreach ($Cid as $id){
            $clubsID[] = Course::where('id',$id)->first()->club;
        }
        foreach ($clubsID as $id){
            $clubs[] = Equestrian_club::where('id',$id)->first();
        }
        if($clubs){
        return response()->json([
            'clubs' => $clubs,
            'status' => true
        ]);
    } else{
            return response()->json([
                'message' => 'no clubs for you',
                'status' => false
            ]);
        }
    }


    public function UserReservation($cID){

        $user_id = Auth::id();
        $coursesID = Course::where('club',$cID)->pluck('id');
        foreach ($coursesID as $cid){
          $reservations[] =  Reservation::where('course_id',$cid)
              ->where('user_id',$user_id)
              ->with('course')
              ->get();
        }


        return response()->json([
            'reservations' => $reservations[0],
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

//
//    public function isReserved(Request $request){
//
//
//        $validate = Validator::make($request->all(), [
//            'club' => 'required',
//            'user_id' => 'required'
//        ]);
//
//        if ($validate->fails()) {
//            return response()->json([
//                'message' => 'Validation Error!',
//                'data' => $validate->errors(),
//                'status' => false
//            ]);
//        }
//        $course_id = Reservation::where('user_id',$request->user_id)
//            ->pluck('course_id');
//        $course_id = collect($course_id)->unique()->values()->all();
//        if($course_id){
//            foreach ($course_id as $id){
//                $club_id[] = Course::where('id', $id)->first()->club;
//            }
//          $club_id = collect($club_id)->unique()->values()->all();
//
//            foreach ($club_id as $id) {
//                if ($id == $request->club) {
//                    return response()->json([
//                        'status' => true
//                    ]);
//                }
//            }
//            return response()->json(['status' => false]);
//
//        }else {
//            return response()->json([
//                'status' => false
//            ]);
//        }
//
//    }


    public function isReserved(Request $request){


        $validate = Validator::make($request->all(), [
            'club' => 'required',
            'user_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }
//        $course_id = Reservation::where('user_id',$request->user_id)
//            ->pluck('course_id');
//        $course_id = collect($course_id)->unique()->values()->all();
//        if($course_id){
//            foreach ($course_id as $id){
//                $club_id[] = Course::where('id', $id)->first()->club;
//            }
//          $club_id = collect($club_id)->unique()->values()->all();
//
//            foreach ($club_id as $id) {
//                if ($id == $request->club) {
//                    return response()->json([
//                        'status' => true
//                    ]);
//                }
//            }
//            return response()->json(['status' => false]);
//
//        }else {
//            return response()->json([
//                'status' => false
//            ]);
//        }

        $booking = Booking::whereHas('service', function ($query) use ($request) {
            $query->where('club_id', $request->club);
        })->where('user_id', $request->user_id)->first();

        $reservation = Reservation::whereHas('course', function ($query) use ($request) {
            $query->where('club', $request->club);
        })->where('user_id', $request->user_id)->first();


        if ($booking || $reservation)

            return response()->json(['status' => true]);
        else
            return response()->json(['status' => false]);
    }


    public function isReservedTrainer(Request $request){


        $validate = Validator::make($request->all(), [
            'trainer_id' => 'required',
            'user_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }
//        $course_id = Reservation::where('user_id',$request->user_id)
//            ->pluck('course_id');
//        $course_id = collect($course_id)->unique()->values()->all();
//        if($course_id){
//            foreach ($course_id as $id){
//                $club_id[] = Course::where('id', $id)->first()->club;
//            }
//          $club_id = collect($club_id)->unique()->values()->all();
//
//            foreach ($club_id as $id) {
//                if ($id == $request->club) {
//                    return response()->json([
//                        'status' => true
//                    ]);
//                }
//            }
//            return response()->json(['status' => false]);
//
//        }else {
//            return response()->json([
//                'status' => false
//            ])
//        }

        $booking = Booking::where('trainer_id', $request->trainer_id)->where('user_id', $request->user_id)->first();

        $reservation = Reservation::whereHas('course', function ($query) use ($request) {
            $query->where('trainer_id', $request->trainer_id);
        })->where('user_id', $request->user_id)->first();


        if ($booking || $reservation)

            return response()->json(['status' => true]);
        else
            return response()->json(['status' => false]);
    }

    public function sendAlert(){

        $now = Carbon::now();
        $corses = Course::where('begin','>',$now)->get();
       foreach ($corses as $is){
           $CarbonDate = Carbon::parse($is->begin);
           $is->begin = $CarbonDate->subDay();
       }
      $rr=[];
       foreach ($corses as $id){
           $RRr = Carbon::parse($id->begin);
           if($RRr->isSameDay($now))
               $rr[] = $id;
            }
       $bb = [];
       foreach ($rr as $id){
           $bb[] = $id->id;
       }

        foreach ($bb as $id){
            $users[] = Reservation::where('course_id',$id)->pluck('user_id');
        }

        $message = 'there is one day left for course beginning';
        foreach ($users as $user){
            echo $user;
             Broadcast(new Users($message,$user));
        }

    }
}
