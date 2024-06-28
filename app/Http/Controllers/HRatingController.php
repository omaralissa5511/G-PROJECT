<?php

namespace App\Http\Controllers;

use App\Models\CLUB\Booking;
use App\Models\CLUB\CRating;
use App\Models\CLUB\Reservation;

use App\Models\CLUB\TRating;
use App\Models\Consultation;
use App\Models\HRating;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class HRatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAllRatingInHealth($health_id)
    {

        $ratings = HRating::where('health_care_id', $health_id)->get();

        return response()->json([
            'ratings' => $ratings,
            'status' => true
        ]);
    }


    public function getAllReviewsInHealth($health_id)
    {
        $reviews = HRating::where('health_care_id', $health_id)
            ->whereNotNull('review')
            ->with('profile.user')
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


    public function getAverageRating($health_id)
    {
        $averageRating = HRating::where('health_care_id', $health_id)->get()->avg('rating');
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
            'health_id' => 'required',
            'profile_id' => 'required',
            'rating' => 'required|integer|between:1,5'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'errors' => $validate->errors(),
                'status' => false
            ]);
        }

        $consultation=Consultation::where('profile_id',$request->profile_id)
            ->where('health_care_id',$request->health_id)->whereNotNull('reply_content')->first();
        if(!$consultation)
            return response()->json([
                'message' => 'You cannot rate because you do not have a responded consultation.',
                'status' => false
            ]);

        $existingRating = HRating::where('health_care_id', $request->health_id)
            ->where('profile_id', $request->profile_id)
            ->first();
        if ($existingRating) {
            return response()->json([
                'message' => 'You have already rated this Health Care.',
                'status' => false
            ]);
        }


        $rating = HRating::create([
            'health_care_id' => $request->health_id,
            'profile_id' => $request->profile_id,
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        $message = 'new Rating have added successfully.';
        Broadcast(new \App\Events\HRating($message));

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
            'profile_id' =>'required',
            'rating' => 'required|integer|between:1,5'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'errors' => $validate->errors(),
                'status' => false
            ]);
        }

        $rating = HRating::find($request->rating_id);

        if (!$rating) {
            return response()->json([
                'message' => 'Rating not found!',
                'status' => false
            ]);
        }

        if ($rating->profile_id != $request->profile_id) {
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
        Broadcast(new \App\Events\HRating($message));

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

        $rating = HRating::find($request->rating_id);

        if (!$rating) {
            return response()->json([
                'message' => 'Rating not found!',
                'status' => false
            ]);
        }

        if ($rating->profile_id != $request->profile_id) {
            return response()->json([
                'message' => 'You are not authorized to delete this rating.',
                'status' => false
            ]);
        }

        $rating->delete();

        $message = 'A rating is deleted';
        Broadcast(new \App\Events\HRating($message));

        return response()->json([
            'message' => 'Rating is deleted successfully.',
            'status' => true
        ]);
    }

    public function userHasReviewInHealth(Request $request){

        $review = HRating::where('health_care_id', $request->health_id)
            ->where('profile_id', $request->profile_id)
            ->whereNotNull('review')->first();

        if($review)
            return response()->json(['status' => true]);
        else
            return response()->json(['status' => false]);
    }

    public function getRatingIDByUser(Request $request){
        $user = HRating::where('profile_id',$request->profile_id)->first();
        $health = HRating::where('health_care_id',$request->health_id)->first();
        if(!($user && $health ))
            return response()->json([
                'message'=>'user not found Rating on this Health Care',
                'status' => false
            ]);
        $rating=HRating::where('profile_id',$request->profile_id)->where('health_care_id',$request->health_id)->first()->id;

        if($rating)
            return response()->json([
                'id'=>$rating,
                'status' => true
            ]);
        else
            return response()->json(['status' => false]);
    }


    public function isReservedHealth(Request $request)
    {


        $validate = Validator::make($request->all(), [
            'health_id' => 'required',
            'profile_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }

        $consultation=Consultation::where('profile_id',$request->profile_id)
            ->where('health_care_id',$request->health_id)->whereNotNull('reply_content')->first();
        if($consultation)
            return response()->json(['status' => true]);
        else
            return response()->json(['status' => false]);
    }
}
