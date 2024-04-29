<?php

namespace App\Http\Controllers;

use App\Models\CLUB\Booking;
use App\Models\CLUB\TrainerTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function addBooking(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'service_id' => 'required|integer',
            'trainer_id' => 'required|integer',
            'trainerTime_id' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }


            $booking = Booking::create($request->all());


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

            $trainerTime = $booking->trainerTime;
            $trainer = $booking->trainer;
            $service = $booking->service;
            $club = $service->club;


            $bookingInfo = [
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'club_id' => $club->id,
                'club_name' => $club->name,
                'service_id' => $service->id,
                'service_name' => $service->name,
                'trainer_id' => $trainer->id,
                'trainer_FName' => $trainer->FName,
                'trainer_lName' => $trainer->lName,
                'booking_status' => $booking->status,
                'booking_date' => $trainerTime->date,
                'start_time' => $trainerTime->start_time,
                'end_time' => $trainerTime->end_time,
            ];

            $bookingDetails[] = $bookingInfo;
        }

        return response()->json([
            'message' => "Get all bookings by user successfully.",
            'bookings' => $bookingDetails,
            'status'=>true
        ]);
    }
}
