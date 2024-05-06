<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\Horse;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class AuctionController extends Controller
{

    public function AddAuction(Request $request)
    {

        $user_id = Auth::id();
        $profile_id = Profile::where('id',$user_id)->first()->id;
        $validate = Validator::make($request->all(), [

            'description' => 'required|string|max:250',
            'initialPrice' => 'required|string|max:250',
            'end' => 'required',
            'begin' => 'required',
            'name' => 'required|string|max:250',
            'category' => 'required|string|max:250',
            'color' => 'required|string|max:250',
            'images' => 'required',
            'birth' => 'required',
            'gender' => 'required',
            'address' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }
        $images = $request->file('images');
        $imagePaths = [];
        foreach ($images as $image) {
            $new_name = rand() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/HORSE/'), $new_name);
            $imagePaths[] = 'images/HORSE/' . $new_name;
        }

        $auction = Auction::create([
            'initialPrice' => $request->initialPrice,
            'description' => $request->description,
            'end' => $request->end,
            'begin' => $request->begin,
            'profile_id' => $profile_id,
        ]);

        $horse = Horse::create([
            'name' => $request->name,
            'category' => $request->category,
            'address' => $request->address,
            'color' => $request->color,
            'birth' => $request->birth,
            'gender' => $request->gender,
            'auction_id' => $auction->id,
            'images' => $imagePaths
        ]);
        $data['auction'] = $auction;
        $data['horse'] = $horse;
        $response = [
            'message' => 'auction is added successfully.',
            'data' => $data,
            'status' => true
        ];
        return response()->json($response);
    }


    public function EditAuction(Request $request,$id)
    {

        $user_id = Auth::id();
        $profile_id = Profile::where('id',$user_id)->first()->id;
        $validate = Validator::make($request->all(), [

            'description' => 'required|string|max:250',
            'initialPrice' => 'required|string|max:250',
            'end' => 'required',
            'begin' => 'required',
            'name' => 'required|string|max:250',
            'category' => 'required|string|max:250',
            'color' => 'required|string|max:250',
            'images' => 'required',
            'birth' => 'required',
            'gender' => 'required',
            'address' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }
        $images = $request->file('images');
        $imagePaths = [];
        foreach ($images as $image) {
            $new_name = rand() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/HORSE/'), $new_name);
            $imagePaths[] = 'images/HORSE/' . $new_name;
        }

        $auction = Auction::where('id',$id)->first();
            $auction->update([
            'initialPrice' => $request->initialPrice,
            'description' => $request->description,
            'end' => $request->end,
            'begin' => $request->begin,
        ]);
          $horse = Horse::where('auction_id',$id)->first();
        $horse -> update([
            'name' => $request->name,
            'category' => $request->category,
            'address' => $request->address,
            'color' => $request->color,
            'birth' => $request->birth,
            'gender' => $request->gender,
            'images' => $imagePaths
        ]);
        $data['auction'] = $auction;
        $data['horse'] = $horse;
        $response = [
            'message' => 'auction is updated successfully.',
            'data' => $data,
            'status' => true
        ];
        return response()->json($response);
    }



    public function showAuctionByID($Id)
    {

        $auction = Auction::find($Id);
        $horseImage = Horse::where('auction_id',$auction->id)->first()->images;
        if ($auction) {

            $response = [
                'auction' => $auction,
                'horseImage' => $horseImage[0],
                'status' => true
            ];
            return $response;
        } else {
            $response = [
                'message' => 'there is no auction',
                'status' => false
            ];
            return $response;
        }
    }

    public function showHorseByID($Id)
    {

        $horses = Horse::find($Id);

        if ($horses) {
            $response = [
                'data' => $horses,
                'status' => true
            ];
            return $response;
        } else {
            $response = [
                'message' => 'there is no horse',
                'status' => false
            ];
            return $response;
        }
    }


    public function AddBid ($Aid,Request $request){

    $lockKey = "auc_lock_{$Aid}";
    $lock = Cache::lock($lockKey, 30);
    $lockAcquired = $lock->get();
    if ($lockAcquired) {

        try {
            $user_id = Auth::id();
            $profile_id = Profile::where('id',$user_id)->first()->id;
            $auctionOWNER = Auction::where('id',$Aid)->first()->profile_id;
            if($auctionOWNER == $profile_id){
                return response()->json([
                    'message' => 'you can not offer a bid because you create the auction',
                    'status' => false
                ]);
            }
            $validate = Validator::make($request->all(), [
                'offeredPrice' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                    'status' => false
                ]);
            }
            $currentBid = Bid::where('auction_id', $Aid)->pluck('offeredPrice');
            $MAX_CurrentBid = collect($currentBid)->max();

            if ($currentBid->isEmpty()) {

                $InitialPrice = Auction::where('id', $Aid)->first()->initialPrice;
                $amountPAY = ($InitialPrice + (($InitialPrice * 5) / 100));
             // return   $request->offeredPrice;
                if ($request->offeredPrice < $amountPAY) {

                    $response = [
                        'your offer is :' => $request->offeredPrice,
                        'message' => 'the amount u should offer is at least: ',
                        'amount' => $amountPAY,
                        'status' => false
                    ];
                    return response()->json($response);
                }
            } else {

                $amountPAY = ($MAX_CurrentBid + (($MAX_CurrentBid * 5) / 100));
                if ($request->offeredPrice < $amountPAY) {

                    $response = [
                        'your offer is :' => $request->offeredPrice,
                        'message' => 'the amount u should offer is at least: ',
                        'amount' => $amountPAY,
                        'status' => false
                    ];
                    return response()->json($response);
                }
            }

            $bid = Bid::create([
                'offeredPrice' => $request->offeredPrice,
                'auction_id' => $Aid,
                'profile_id' => $profile_id
            ]);
            $response = [
                'message' => 'bid is offered successfully.',
                'data' => $bid,
                'status' => true
            ];
            return response()->json($response);

        } finally {
            $lock->release();
        }


    }else {
        return response()->json(['message' => 'Unable to acquire the file lock. Try again later.'], 403);
    }

    }

    public function getCurrentBid($Aid,Request $request){

        $currentBid = Bid::where('auction_id',$Aid)->pluck('offeredPrice');
        $MAX_CurrentBid = collect($currentBid)->max();
        $MAX_CurrentBid_owner_id = Bid::where
        ('offeredPrice','=',70000)->first()->profile_id;
        $ownerOFBigBid = Profile::find($MAX_CurrentBid_owner_id);
        if($currentBid->isEmpty()){

            $InitialPrice = Auction::where('id',$Aid)->first()->initialPrice;
            $amountPAY = ($InitialPrice+(($InitialPrice*5)/100));
               $data['CURRENT BID : '] = $InitialPrice;
               $data['THE NEXT OFFER : '] = $amountPAY;

                return response()->json($data);
        }else {

            $amountPAY = ($MAX_CurrentBid+(($MAX_CurrentBid*5)/100));
            $data['CURRENT BID : '] = $MAX_CurrentBid;
            $data['ownerOFBigBid'] = $ownerOFBigBid;
            $data['THE NEXT OFFER : '] = $amountPAY;

                return response()->json($data);
        }
    }

    public function getBuyersIN_Auction($id)
    {
         $profiles = Bid::where('auction_id', $id)->pluck('profile_id');
          $TheBuyers_id = collect($profiles)->unique()->values()->all();
        foreach ($TheBuyers_id as $Pid){

            $TheBuyers[] = Profile::find($Pid);
            $TheBuyers[] = Bid::where('profile_id',$Pid)->where
            ('auction_id',$id)->orderBy('id','desc')->first()->offeredPrice;
        }
        return response()->json($TheBuyers);
    }

    public function getTodayAuctions(){

        $today = Carbon::now();
        $auctions = Auction::query()
            ->whereDate('begin','<=',$today)
            ->whereDate('end','>=',$today)
            ->where('status','confirmed')->get();

        if($auctions->isEmpty()){
            $response = [
                'message' => 'no active auctions now.',
                'status' => false
            ];
        return response()->json($response);
        }else {
            $response = [
                'message' => 'get successfully.',
                'auctions' => $auctions,
                'status' => true
            ];
            return response()->json($response);
        }
    }


    public function upcoming(){

        $today = Carbon::now();
        $todayPlusOne = $today->copy()->addDay(1);
        $towMonthLater = $today->copy()->addMonth(2);
        $auctions = Auction::query()
            ->whereDate('begin','>=',$todayPlusOne)
            ->whereDate('end','<=',$towMonthLater)
            ->where('status','confirmed')->get();

        if($auctions->isEmpty()){
            $response = [
                'message' => 'no active auctions now.',
                'status' => false
            ];
            return response()->json($response);
        }else {
            $response = [
                'message' => 'get successfully.',
                'auctions' => $auctions,
                'status' => true
            ];
            return response()->json($response);
        }
    }
}
