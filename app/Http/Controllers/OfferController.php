<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Events\Offers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OfferController extends Controller
{

    public function addOffer(Request $request){

        $validate = Validator::make($request->all(), [
            'health_id' => 'required',
            'offer_value' => 'required',
            'begin' => 'required|date',
            'end' => 'required|date',
           ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }

        $crossOffer = Offer::where('health_care_id', $request->health_id)
            ->where(function ($query) use ($request) {
                $query->where('begin', '<=', $request->end)
                    ->where('end', '>=', $request->begin);
            })
            ->first();


        if ($crossOffer) {
            return response()->json([
                'message' => 'There is already an cross offer.',
                'status' => false
            ]);
        }

        $offer = Offer::create([
            'health_care_id'=> $request->health_id,
            'offer_value'=> $request->offer_value,
            'description'=> $request->description,
            'begin'=> $request->begin,
            'end'=> $request->end,
        ]);
        $message = 'offer is created successfully.';
        broadcast(new Offers($message));

        return response()->json([
            'message' => 'Offer created successfully.',
            'offer' => $offer,
            'status' => true
        ]);
    }

    public function deleteOffer($id)
    {
        $offer = Offer::find($id);

        if (!$offer) {
            return response()->json([
                'message' => 'Offer not found.',
                'status' => false
            ]);
        }

        $offer->delete();
$message = 'offer is deleted successfully.';
        broadcast(new Offers($message));
        return response()->json([
            'message' => 'Offer deleted successfully.',
            'status' => true
        ]);
    }

    public function getOffersToday(){

        $today = Carbon::now();
        $offers = Offer::query()
            ->whereDate('begin','<=',$today)
            ->whereDate('end','>=',$today)->get();

        if($offers->isEmpty()){
            return response()->json([
                'message' => 'no active offers now.',
                'status' => false
            ]);
        }else {
            foreach ($offers as $offer){
                $offer->image=$offer->health_care->profile_image;
                $offer->name=$offer->health_care->name;
                unset($offer->health_care);
            }
            return response()->json([
                'message' => 'get successfully.',
                'offers' => $offers,
                'status' => true
            ]);
        }
    }
}
