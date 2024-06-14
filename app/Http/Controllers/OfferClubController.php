<?php

namespace App\Http\Controllers;

use App\Models\CLUB\OfferClub;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OfferClubController extends Controller
{

    public function addOffer(Request $request){

        $validate = Validator::make($request->all(), [
            'club_id' => 'required',
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

        $crossOffer = OfferClub::where('club_id', $request->club_id)
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

        $offer = OfferClub::create([
            'club_id'=> $request->club_id,
            'offer_value'=> $request->offer_value,
            'description'=> $request->description,
            'begin'=> $request->begin,
            'end'=> $request->end,
        ]);

        return response()->json([
            'message' => 'Offer created successfully.',
            'offer' => $offer,
            'status' => true
        ]);
    }

    public function deleteOffer($id)
    {
        $offer = OfferClub::find($id);

        if (!$offer) {
            return response()->json([
                'message' => 'Offer not found.',
                'status' => false
            ]);
        }

        $offer->delete();

        return response()->json([
            'message' => 'Offer deleted successfully.',
            'status' => true
        ]);
    }

    public function getOffersToday(){

        $today = Carbon::now();
        $offers = OfferClub::query()
            ->whereDate('begin','<=',$today)
            ->whereDate('end','>=',$today)->get();

        if($offers->isEmpty()){
            return response()->json([
                'message' => 'no active offers now.',
                'status' => false
            ]);
        }else {
            foreach ($offers as $offer){
                $offer->image=$offer->club->profile;
                $offer->name=$offer->club->name;
                unset($offer->club);
            }
            return response()->json([
                'message' => 'get successfully.',
                'offers' => $offers,
                'status' => true
            ]);
        }
    }
}
