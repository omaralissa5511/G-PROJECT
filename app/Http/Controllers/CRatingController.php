<?php

namespace App\Http\Controllers;

use App\Models\CLUB\Booking;
use App\Models\CLUB\CRating;
use App\Models\CLUB\Reservation;

use App\Models\CLUB\TRating;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class CRatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAllRatingInClub($club_id)
    {

        $ratings = CRating::where('club_id', $club_id)->get();

        return response()->json([
            'ratings' => $ratings,
            'status' => true
        ]);
    }


    public function getAllReviewsInClub($club_id)
    {
        $reviews = CRating::where('club_id', $club_id)
            ->whereNotNull('review')
            ->with('user.profiles')
            ->get();

        // تحويل الوقت إلى شكل مقروء بشكل أكبر
        foreach ($reviews as $review) {
            $review->review_time = Carbon::parse($review->created_at)->diffForHumans();
        }

        return response()->json([
            'reviews' => $reviews,
            'status' => true
        ]);
    }


    public function getAverageRating($club_id)
    {
        $averageRating = CRating::where('club_id', $club_id)->get()->avg('rating');
        return response()->json([
            'averageRating' => $averageRating,
            'status' => true
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function createRating(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'club_id' => 'required',
            'user_id' => 'required',
            'rating' => 'required|integer|between:1,5'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'errors' => $validate->errors(),
                'status' => false
            ]);
        }


        $booking = Booking::whereHas('service', function ($query) use ($request) {
            $query->where('club_id', $request->club_id);
        })->where('user_id', $request->user_id)->first();

        $reservation = Reservation::whereHas('course', function ($query) use ($request) {
            $query->where('club', $request->club_id);
        })->where('user_id', $request->user_id)->first();

        if (!($booking || $reservation)) {
            return response()->json([
                'message' => 'You can only rate if you have made a booking previously in this club.',
                'status' => false
            ]);
        }



        $existingRating = CRating::where('club_id', $request->club_id)
            ->where('user_id', $request->user_id)
            ->first();
        if ($existingRating) {
            return response()->json([
                'message' => 'You have already rated this club.',
                'status' => false
            ]);
        }


        $rating = CRating::create([
            'club_id' => $request->club_id,
            'user_id' => $request->user_id,
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        $message = 'new Rating have added successfully.';
        Broadcast(new \App\Events\CRating($message));

        return response()->json([
            'message' => 'Rating is created successfully.',
            'rating' => $rating,
            'status' => true
        ]);
    }



    public function updateRating(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'rating_id' => 'required',
            'user_id' =>'required',
            'rating' => 'required|integer|between:1,5'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'errors' => $validate->errors(),
                'status' => false
            ]);
        }

        $rating = CRating::find($request->rating_id);

        if (!$rating) {
            return response()->json([
                'message' => 'Rating not found!',
                'status' => false
            ]);
        }

        if ($rating->user_id != $request->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to update this rating.',
                'status' => false
            ]);
        }

        $rating->update([
            'rating' =>$request->rating,
            'review' =>$request->review
        ]);
        $message = 'A rating is updated';
        Broadcast(new \App\Events\CRating($message));

        return response()->json([
            'message' => 'Rating is updated successfully.',
            'rating' => $rating,
            'status' => true
        ]);
    }

    public function deleteRating(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'rating_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'errors' => $validate->errors(),
                'status' => false
            ]);
        }

        $rating = CRating::find($request->rating_id);

        if (!$rating) {
            return response()->json([
                'message' => 'Rating not found!',
                'status' => false
            ]);
        }

        if ($rating->user_id != $request->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to delete this rating.',
                'status' => false
            ]);
        }

        $rating->delete();

        $message = 'A Rating is deleted';
        Broadcast(new \App\Events\CRating($message));

        return response()->json([
            'message' => 'Rating is deleted successfully.',
            'status' => true
        ]);
    }

    public function userHasReviewInClub(Request $request){

        $review = CRating::where('club_id', $request->club_id)
            ->where('user_id', $request->user_id)
            ->whereNotNull('review')->first();

        if($review)
            return response()->json(['status' => true]);
        else
            return response()->json(['status' => false]);
    }

    public function getRatingIDByUser(Request $request){
        $user = CRating::where('user_id',$request->user_id)->first();
        $club = CRating::where('club_id',$request->club_id)->first();
        if(!($user && $club ))
            return response()->json([
                'message'=>'user not found Rating on this club',
                'status' => false
            ]);
        $rating=CRating::where('user_id',$request->user_id)->where('club_id',$request->club_id)->first()->id;

        if($rating)
            return response()->json([
                'id'=>$rating,
                'status' => true
            ]);
        else
            return response()->json(['status' => false]);
    }


}
