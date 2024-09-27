<?php

namespace App\Http\Controllers;

use App\Models\CLUB\Booking;
use App\Models\CLUB\Equestrian_club;
use App\Models\CLUB\Service;
use App\Models\CLUB\Trainer;
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
        $message = 'new Booking have added successfully.';
        Broadcast(new \App\Events\Booking($message));

        $message = 'TrainerTime have updated.';
        Broadcast(new \App\Events\TrainerTime($message));
        // notification
        $service_name = Service::where('id', $booking->service_id)->select('name', 'club_id')->first();
        $club_name = Equestrian_club::where('id',$service_name->club_id)->first('name');
        $trainer_name = Trainer::where('id',$booking->trainer_id)->pluck('FName')->first();
        $user1 = User::where('id',$booking->user_id)->first();
        $notificationService = new \App\Services\Api\NotificationService();
        $notificationService->send($user1, 'Booking created successfully.', 'You have booked '.$service_name->name.' service and trainer '.$trainer_name.' at '.$club_name->name);
        // end notification
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

            $totalPrice = 0;
            $trainerTimesInfo=[];

            foreach ($trainerTimes as $trainerTime){
                $trainertimesss=[
                'trainerTimeID'=>$trainerTime->id,
                'booking_date' => $trainerTime->date,
                'start_time' => $trainerTime->start_time,
                'end_time' => $trainerTime->end_time,
                    ];
                $totalPrice=$totalPrice + $trainerTime->price;
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
                'amount'=> $totalPrice,
                'trainerTimesInfo'=> $trainerTimesInfo,

            ];

        return response()->json([
            'message' => "Get all bookings by user successfully.",
            'bookings' => $bookingInfo,
            'status'=>true
        ]);
    }

    public function cancelBookingTime(Request $request)
    {
        $trainerTime = TrainerTime::find($request->trainerTime_id);

        if (!$trainerTime) {
            return response()->json([
                'message' => 'Trainer time not found!',
                'status' => false
            ]);
        }

        $currentUserId = $request->user_id;
        if ($trainerTime->booking->user_id != $currentUserId) {
            return response()->json([
                'message' => 'You are not authorized to cancel this booking time.',
                'status' => false
            ]);
        }

        $now = \Carbon\Carbon::now();
        $bookingDateTime = \Carbon\Carbon::parse($trainerTime->date . ' ' . $trainerTime->start_time);
        $diffInHours = $now->diffInHours($bookingDateTime);
        if ($diffInHours < 24) {
            return response()->json([
                'message' => 'You cannot cancel a booking time less than 24 hours before it starts.',
                'status' => false
            ]);
        }
        $bookingId = $trainerTime->booking_id;

        $trainerTime->update([
            'booking_id' => null,
            'is_available' => true
        ]);
        $message = 'TrainerTime updated.';
        Broadcast(new \App\Events\TrainerTime($message));


        $otherTrainerTimes = TrainerTime::where('booking_id', $bookingId)->count();
        if ($otherTrainerTimes == 0) {
           Booking::destroy($bookingId);
            $message = 'Booking deleted';
            Broadcast(new \App\Events\Booking($message));
        }


        return response()->json([
            'message' => 'Booking time cancelled successfully.',
            'status' => true
        ]);
    }

    public function deleteBooking(Request $request)
    {
        $booking = Booking::find($request->booking_id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found!',
                'status' => false
            ]);
        }

        $currentUserId = $request->user_id;
        if ($booking->user_id != $currentUserId) {
            return response()->json([
                'message' => 'You are not authorized to delete this booking.',
                'status' => false
            ]);
        }

        $now = \Carbon\Carbon::now();
        $bookingDateTime = \Carbon\Carbon::parse($booking->trainerTimes()->min('date') . ' ' . $booking->trainerTimes()->min('start_time'));
        $diffInHours = $now->diffInHours($bookingDateTime);
        if ($diffInHours < 24) {
            return response()->json([
                'message' => 'You cannot delete a booking less than 24 hours before it starts.',
                'status' => false
            ]);
        }

        TrainerTime::where('booking_id', $request->booking_id)->update([
            'booking_id' => null,
            'is_available' => true
        ]);
        $message = 'TrainerTime updated.';
        Broadcast(new \App\Events\TrainerTime($message));

        $booking->delete();
        $message = 'Booking deleted';
        Broadcast(new \App\Events\Booking($message));

        return response()->json([
            'message' => 'Booking deleted successfully.',
            'status' => true
        ]);
    }
}
