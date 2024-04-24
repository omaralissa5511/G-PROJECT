<?php

namespace App\Http\Controllers;

use App\Models\CLUB\TRating;
use App\Models\CLUB\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TRatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAllRatingInTrainer($trainer_id)
    {

        $ratings = TRating::where('trainer_id', $trainer_id)->get();

        return response()->json([
            'ratings' => $ratings,
            'status' => true
        ]);
    }

    public function getAverageRating($trainer_id)
    {
        $averageRating = TRating::where('trainer_id', $trainer_id)->get()->avg('rating');
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
            'trainer_id' => 'required',
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

        // تحقق من الحجز
        $booking = Reservation::whereHas('course', function ($query) use ($request) {
            $query->where('trainer_id', $request->trainer_id);
        })->where('user_id', $request->user_id)->first();

        if (!$booking) {
            return response()->json([
                'message' => 'You can only rate if you have made a booking previously.',
                'status' => false
            ]);
        }

        // تحقق من أن المستخدم لم يقم بتقييم المدرب من قبل
        $existingRating = TRating::where('trainer_id', $request->trainer_id)
            ->where('user_id', $request->user_id)
            ->first();
        if ($existingRating) {
            return response()->json([
                'message' => 'You have already rated this trainer.',
                'status' => false
            ]);
        }

        $rating = TRating::create([
            'trainer_id' => $request->trainer_id,
            'user_id' => $request->user_id,
            'rating' => $request->rating
        ]);

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

        $rating = TRating::find($request->rating_id);

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

        $rating = TRating::find($request->rating_id);

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

        return response()->json([
            'message' => 'Rating is deleted successfully.',
            'status' => true
        ]);
    }

}
