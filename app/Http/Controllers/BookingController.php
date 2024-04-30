<?php

namespace App\Http\Controllers;

use App\Models\CLUB\Booking;
use App\Models\CLUB\TrainerTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TrainerController;

class BookingController extends Controller
{

    public function addBooking(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'service_id' => 'required|integer',
            'trainer_id' => 'required|integer',
            'trainerTime_ids' => 'required|array',
            'status' => 'required|boolean',
        ]);

        $trainerTime_ids=$request->trainerTime_ids;

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }
        // التحقق من أن الوقت موجود ومتاح
        foreach ($trainerTime_ids as $trainerTime_id)
        {
            $trainerTime = TrainerTime::find($trainerTime_id);
            if (!$trainerTime || !$trainerTime->is_available) {
                return response()->json([
                    'message' => "Trainer time is not available.",
                    'status' => false
                ]);
            }
        }

        $booking = Booking::create([
            'user_id' => $request->user_id,
            'service_id' => $request->service_id,
            'trainer_id' => $request->trainer_id,
            'status' => $request->status,
        ]);

        foreach ($trainerTime_ids as $trainerTime_id)
        {
            TrainerTime::where('id',$trainerTime_id)->update([
                'booking_id'=>$booking->id,
                'is_available'=> false
            ]);
        }

            return response()->json([
                'message' => "Booking created successfully.",
                'booking' => $booking,
            ]);

    }


    public function getAllBookingByUser($id)
    {

        $bookings = Booking::where('user_id', $id)->get();

        $bookingDetails = [];

        foreach ($bookings as $booking) {

            $service = $booking->service;
            $club = $service->club;

            $bookingInfo = [
                'booking_id' => $booking->id,
                'club_id' => $club->id,
                'club_name' => $club->name,
                'club_image' =>$club->profile,
                'club_description' =>$club->description,
            ];

            $bookingDetails[] = $bookingInfo;
        }

        return response()->json([
            'message' => "Get all bookings by user successfully.",
            'bookings' => $bookingDetails,
            'status'=>true
        ]);
    }

    public function getBooking($booking_id)
    {

        $booking = Booking::where('id', $booking_id)->first();

            $trainerTimes = $booking->trainerTimes;
            $trainer = $booking->trainer;
            $service = $booking->service;
            $club = $service->club;

            $trainerTimesInfo=[];

            foreach ($trainerTimes as $trainerTime){
                $trainertimesss=[
                'booking_date' => $trainerTime->date,
                'start_time' => $trainerTime->start_time,
                'end_time' => $trainerTime->end_time,
                    ];
                $trainerTimesInfo[]=$trainertimesss;

            }


            $bookingInfo = [
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'club_id' => $club->id,
                'club_name' => $club->name,
                'club_image' =>$club->profile,
                'club_description' =>$club->description,
                'service_id' => $service->id,
                'service_name' => $service->name,
                'service_image' => $service->image,
                'trainer_id' => $trainer->id,
                'trainer_FName' => $trainer->FName,
                'trainer_lName' => $trainer->lName,
                'trainer_image' => $trainer->image,
                'booking_status' => $booking->status,
                'trainerTimesInfo'=> $trainerTimesInfo
            ];

        return response()->json([
            'message' => "Get all bookings by user successfully.",
            'bookings' => $bookingInfo,
            'status'=>true
        ]);
    }

}
