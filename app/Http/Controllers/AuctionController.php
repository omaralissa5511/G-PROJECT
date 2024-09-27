<?php

namespace App\Http\Controllers;

use App\Events\Bids;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\Horse;
use App\Models\Insurance;
use App\Models\Profile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class AuctionController extends Controller
{


  public function Auctions_that_A_User_Participates_in(){

        $userId = Auth::id();
        $profile_id = Profile::where('user_id',$userId)->first()->id;
        $auction_ids = Bid::where('profile_id',$profile_id)->get('auction_id');

        $collection = collect($auction_ids);
        $auction_ids  = $collection->unique();
        $auctions = [];
        foreach ($auction_ids as $id){
            $auctions[] = Auction::where('id',$id->auction_id)->with('horses')->first();
        }
        $rr = [];
        if($auctions){

            foreach ($auctions as $auc)
            {  $auc->profile_id;
            $theOwner = Profile::where('id',$auc->profile_id)->first()->FName;
            $user_id = Profile::where('id',$auc->profile_id)->first()->user_id;
            $theOwnerEmail = User::where('id',$user_id)->first()->email;

            $TimeNow =  Carbon::today();
            $end = Carbon::parse($auc->end) ;
            $begin = Carbon::parse($auc->begin) ;

           $offeredPrice = Bid::where('auction_id',$auc->id)
                ->where('profile_id',$profile_id)
                ->latest()->first()->offeredPrice;
            if($TimeNow>=$begin && $TimeNow<=$end){

               $timeNow = Carbon::now('Asia/Damascus');
               $endHours = Carbon::parse($auc->limit) ;
               $DiffInHours =  $timeNow->DiffInHours($endHours)-3;
               $DiffInMinutes =  $timeNow->DiffInMinutes($endHours)-180;
               $diff_in_days = $timeNow->DiffInDays($end) ;
               $DiffInMinutes = $DiffInMinutes%60;
               $response = [
                   'days left : ' => $diff_in_days,
                   'hours left : ' => $DiffInHours,
                   'minutes left : ' => $DiffInMinutes,
               ];

               $auc['timeLeft'] = $response;
               $auc['theOwner'] = $theOwner;
               $auc['theOwnerEmail'] = $theOwnerEmail;
                  if($offeredPrice) {
                    $auc['offeredPrice'] = $offeredPrice;
                }else{
                    $auc['offeredPrice'] = null;
                }

           }else {
                  $response = [
                   'days left : ' => 0,
                   'hours left : ' => 0,
                   'minutes left : ' => 0,
               ];

               $auc['timeLeft'] = $response;
                $auc['theOwner'] = $theOwner;
                $auc['theOwnerEmail'] = $theOwnerEmail;
                  if($offeredPrice) {
                    $auc['offeredPrice'] = $offeredPrice;
                } else{
                    $auc['offeredPrice'] = null;
                }
           }
            }

            $response = [
                'message' => 'auction is founded successfully.',
                'auctions' => $auctions,
                'status' => true
            ];
            return response()->json($response);
        }else{
            $response = [
                'message' => 'auction is not  founded successfully.',

                'status' => false
            ];
            return response()->json($response);
        }

    }
    
      public function AddAuction(Request $request) {

         $user_id = $request->user_id;

        $profile_id = Profile::where('user_id',$user_id)->first()->id;
        $validate = Validator::make($request->all(), [
            'description' => 'required|string|max:250',
            'initialPrice' => 'required|string|max:250',
            'end' => 'required',
            'begin' => 'required',
            'name' => 'required|string|max:250',
            'category' => 'required|string|max:250',
            'color' => 'required|string|max:250',
            'images' => 'required',
           // 'video'=>'required|file|max:10240',
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




        $limitTime = Carbon::createFromFormat('H:i:s', '23:00:00');
        $auction = Auction::create([
            'initialPrice' => $request->initialPrice,
            'description' => $request->description,
            'end' => $request->end,
            'begin' => $request->begin,
            'limit' =>  $limitTime,
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
            'images' => $imagePaths,
           // 'video'=> $realPath
        ]);
         if($request->hasFile('video')){
              $file_extension = $request->video->getClientOriginalExtension();
        $filename = time() . '.' . $file_extension;
        $path = public_path('videos/auction/');
        $request->video->move($path, $filename);
        $realPath = 'videos/auction/'.$filename;
        $horse->update(['video'=>$realPath]);
        }
      

        $data['auction'] = $auction;
        $data['horse'] = $horse;
        $response = [
            'message' => 'auction is added successfully.',
            'data' => $data,
            'status' => true
        ];
        return response()->json($response);
    }




    // public function AddAuction(Request $request) {

    //      $user_id = $request->user_id;

    //     $profile_id = Profile::where('user_id',$user_id)->first()->id;
    //     $validate = Validator::make($request->all(), [
    //         'description' => 'required|string|max:250',
    //         'initialPrice' => 'required|string|max:250',
    //         'end' => 'required',
    //         'begin' => 'required',
    //         'name' => 'required|string|max:250',
    //         'category' => 'required|string|max:250',
    //         'color' => 'required|string|max:250',
    //         'images' => 'required',
    //         //'video'=>'required|file|max:10240',
    //         'birth' => 'required',
    //         'gender' => 'required',
    //         'address' => 'required'
    //     ]);

    //     if ($validate->fails()) {
    //         return response()->json([
    //             'message' => 'Validation Error!',
    //             'data' => $validate->errors(),
    //             'status' => false
    //         ]);
    //     }
    //     $images = $request->file('images');
    //     $imagePaths = [];
    //     foreach ($images as $image) {
    //         $new_name = rand() . '.' . $image->getClientOriginalExtension();
    //         $image->move(public_path('images/HORSE/'), $new_name);
    //         $imagePaths[] = 'images/HORSE/' . $new_name;
    //     }

      
    //         $file_extension = $request->video->getClientOriginalExtension();
    //     $filename = time() . '.' . $file_extension;
    //     $path = public_path('videos/auction/');
    //     $request->video->move($path, $filename);
    //     $realPath = 'videos/auction/'.$filename;
    //   }

    //     $limitTime = Carbon::createFromFormat('H:i:s', '23:00:00');
    //     $auction = Auction::create([
    //         'initialPrice' => $request->initialPrice,
    //         'description' => $request->description,
    //         'end' => $request->end,
    //         'begin' => $request->begin,
    //         'limit' =>  $limitTime,
    //         'profile_id' => $profile_id,
    //     ]);

    //     $horse = Horse::create([
    //         'name' => $request->name,
    //         'category' => $request->category,
    //         'address' => $request->address,
    //         'color' => $request->color,
    //         'birth' => $request->birth,
    //         'gender' => $request->gender,
    //         'auction_id' => $auction->id,
    //         'images' => $imagePaths,
    //         'video'=> $realPath
    //     ]);

    //     $data['auction'] = $auction;
    //     $data['horse'] = $horse;
    //     $response = [
    //         'message' => 'auction is added successfully.',
    //         'data' => $data,
    //         'status' => true
    //     ];
    //     $message='add new auction';
    //     Broadcast(new \App\Events\Auction($message));
    //     return response()->json($response);
    // }


    public function EditAuction(Request $request,$id)
    {

//        $user_id = Auth::id();
//        $profile_id = Profile::where('id',$user_id)->first()->id;
        $validate = Validator::make($request->all(), [

            'description' => 'required|string|max:250',
            'initialPrice' => 'required|string|max:250',
            'end' => 'required',
            'begin' => 'required',
            'name' => 'required|string|max:250',
            'category' => 'required|string|max:250',
            'color' => 'required|string|max:250',
            'images' => 'required',
            'video'=>'required',
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


        $file_extension = $request->video->getClientOriginalExtension();
        $filename = time() . '.' . $file_extension;
        $path = public_path('videos/auction/');
        $request->video->move($path, $filename);
        $realPath = 'videos/auction/'.$filename;


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
            'images' => $imagePaths,
            'video'=>$realPath
        ]);
        $data['auction'] = $auction;
        $data['horse'] = $horse;
        $response = [
            'message' => 'auction is updated successfully.',
            'data' => $data,
            'status' => true
        ];
        
        $message='edit auction';
        Broadcast(new \App\Events\Auction($message));
        return response()->json($response);
    }



    public function showAuctionByID($Id)
    {
        $auctions = Auction::query()
            ->where('id',$Id)
            ->where('status','confirmed')
            ->with('horses', 'profile.user')
            ->first();

        if ($auctions) {

            $response = [
                'auction' => $auctions,
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
            $profile_id = Profile::where('user_id',$user_id)->first()->id;
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
            $name = Profile::where('id',$profile_id)->first()->FName;
            $response = [
                'message' => 'bid is offered successfully.',
                'data' => $bid,
                'status' => true
            ];
            $message = 'new bid is offered successfully.';
            Broadcast(new Bids($bid,$name,$message));
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
        if($MAX_CurrentBid){
        $MAX_CurrentBid_owner_id = Bid::where
        ('offeredPrice','=',$MAX_CurrentBid)->first()->profile_id;
        $ownerOFBigBid = Profile::find($MAX_CurrentBid_owner_id);
        }
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
          $TheBuyers2=[];
        foreach ($TheBuyers_id as $Pid){

            $TheBuyers2[] = Profile::where('id',$Pid)->with('bids')->first();
        }
        $response = [
            'Buyers' => $TheBuyers2,
            'status' => true
        ];
        return response()->json($response);
    }

    public function getTodayAuctions(){

        $today = Carbon::now();
        $auctions = Auction::query()
            ->whereDate('begin','<=',$today)
            ->whereDate('end','>=',$today)
            ->where('status','confirmed')
            ->with(['horses', 'profile' => function ($query) {
                $query->addSelect(['email' => User::select('email')  //لاضافة email لمعلومات ال profile
                    ->whereColumn('id', 'profiles.user_id')
                ]);
            }])
            ->get();

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



    public function upcomingToday_A(){

        $today = Carbon::now();
        $auctions = Auction::query()
            ->whereDate('begin','<=',$today)
            ->where('status','confirmed')->get();

        if($auctions->isEmpty()){
            $response = [
                'message' => 'no active auctions today.',3
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



    public function upcoming_A(){

        $today = Carbon::now();
        $todayPlusOne = $today->copy()->addDay(1);
        $towMonthLater = $today->copy()->addMonth(3);
        $auctions = Auction::query()
            ->whereDate('begin','>=',$todayPlusOne)
            ->whereDate('end','<=',$towMonthLater)
            ->where('status','confirmed')->get();


        if($auctions->isEmpty()){
            $response = [
                'message' => 'there is no upcoming auctions.',
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
        $towMonthLater = $today->copy()->addMonth(3);
        $auctions = Auction::query()
            ->whereDate('begin','>=',$todayPlusOne)
            ->whereDate('end','<=',$towMonthLater)
            ->where('status','confirmed')
            ->pluck('begin');


        if($auctions->isEmpty()){
            $response = [
                'message' => 'no active auctions now.',
                'status' => false
            ];
            return response()->json($response);
        }else {
            $dates = collect($auctions)->map(function ($date) {
                return ['begin_time' => $date];
            });
              $collecton = collect($dates);
            $collecton = $collecton->unique();
            $response = [
                'message' => 'get successfully.',
                'dates' => $collecton,
                'status' => true
            ];
            return response()->json($response);
        }
    }


    public function upcoming2(Request $request){

        $date =  $request->date;
        $auctions = Auction::query()
            ->whereDate('begin','=',$date)
            ->where('status','confirmed')
            ->with('horses', 'profile')
            ->get();

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


    public function upcomingToday(){

        $today = Carbon::now();
        $auctions = Auction::query()
            ->whereDate('begin','<=',$today)
            ->where('status','confirmed')
            ->pluck('begin');


        if($auctions->isEmpty()){
            $response = [
                'message' => 'no active auctions today.',3
            ];
            return response()->json($response);
        }else {
            $dates = collect($auctions)->map(function ($date) {
                return ['begin_time' => $date];
            });
            $response = [
                'message' => 'get successfully.',
                'dates' => $dates,
                'status' => true
            ];
            return response()->json($response);
        }
    }



    public function OperationTime($AID){

        $timeNow = Carbon::now('Asia/Damascus');
        $auction = Auction::findOrFail($AID);
        $end = Carbon::parse($auction->end) ;
        $endHours = Carbon::parse($auction->limit) ;
          $DiffInHours =  $timeNow->DiffInHours($endHours)-3;
        $DiffInMinutes =  $timeNow->DiffInMinutes($endHours)-180;
         $diff_in_days = $timeNow->DiffInDays($end) ;
        $DiffInMinutes = $DiffInMinutes%60;
        $response = [
            'days left : ' => $diff_in_days,
            'hours left : ' => $DiffInHours,
            'minutes left : ' => $DiffInMinutes,
        ];
        return response()->json($response);

    }

    public function addInsurance (Request $request){

        $validate = Validator::make($request->all(), [
            'Auction_id' => 'required',
            'insurance' => 'required',

        ]);
        $user_id = Auth::id();
        $profile_id = Profile::where('user_id',$user_id)->first()->id;
        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }

        $insurance = Insurance::create([
            'insurance' => $request->insurance,
            'auction' => $request->Auction_id,
            'profile_id' =>$profile_id
        ]);
        return response()->json([
            'message' => 'done',
            'the insurance'=> $insurance,
            'status' => true
        ]);
    }


 public function A_bids($Aid){

        $bids = Bid::where('auction_id',$Aid)->get();
        foreach ($bids as $bid){
            $name = Profile::where('id',$bid->profile_id)->first()->FName;
            $photo = Profile::where('id',$bid->profile_id)->first()->profile;
            $bid->First_Name = $name;
            $bid->profile_image = $photo;
        }

        return response()->json(['bids' => $bids]);

    }

    public function winner($id){
        $b=Bid::where('auction_id',$id)->first();
        if($b){
        $bid=Bid::where('auction_id',$id)->orderBy('offeredPrice','desc')->with('profile')->first();
        $user=$bid->profile->user;
        $bid->profile->email=$user->email;
        $bid->profile->mobile=$user->mobile;
        unset($bid->profile->user);
        return response()->json([
            'winner'=> $bid,
            'status' => true
        ]);
    }
        else
         return response()->json([
            'message'=> 'not found bids',
            'status' => false
        ]);

    }
    
     public function Auctions_that_A_User_WIN(){

        $userId = Auth::id();
        $profile_id = Profile::where('user_id',$userId)->first()->id;
        $aucs['auctions'] = Auction::where('winner_id',$profile_id)->with('horses')->get();

        foreach ($aucs['auctions'] as $auc ){
            $theOwner = Profile::where('id',$auc->profile_id)->first()->FName;
            $user_id = Profile::where('id',$auc->profile_id)->first()->user_id;
            $theOwnerEmail = User::where('id',$user_id)->first()->email;
            $auc['theOwner'] = $theOwner;
            $auc['theOwnerEmail'] = $theOwnerEmail;
        }
        return $aucs;
    }

    // public function Auctions_that_A_User_Participates_in(){

    //     $userId = Auth::id();
    //     $profile_id = Profile::where('user_id',$userId)->first()->id;
    //     $auction_ids = Bid::where('profile_id',$profile_id)->get('auction_id');

    //     $collection = collect($auction_ids);
    //     $auction_ids  = $collection->unique();
    //     $auctions = [];
    //     foreach ($auction_ids as $id){
    //         $auctions[] = Auction::where('id',$id->auction_id)->with('horses')->first();
    //     }
    //     $rr = [];
    //     if($auctions){

    //         foreach ($auctions as $auc)
    //         {
    //         $theOwner = Profile::where('id',$auc->profile_id)->first()->FName;
    //         $user_id = Profile::where('id',$auc->profile_id)->first()->user_id;
    //         $theOwnerEmail = User::where('id',$user_id)->first()->email;

    //         $TimeNow =  Carbon::today();
    //         $end = Carbon::parse($auc->end) ;
    //         $begin = Carbon::parse($auc->begin) ;

    //       $offeredPrice = Bid::where('auction_id',$auc->id)
    //             ->where('profile_id',$profile_id)
    //             ->latest()->offeredPrice;
    //         if($TimeNow>=$begin && $TimeNow<=$end){

    //           $timeNow = Carbon::now('Asia/Damascus');
    //           $endHours = Carbon::parse($auc->limit) ;
    //           $DiffInHours =  $timeNow->DiffInHours($endHours)-3;
    //           $DiffInMinutes =  $timeNow->DiffInMinutes($endHours)-180;
    //           $diff_in_days = $timeNow->DiffInDays($end) ;
    //           $DiffInMinutes = $DiffInMinutes%60;
    //           $response = [
    //               'days left : ' => $diff_in_days,
    //               'hours left : ' => $DiffInHours,
    //               'minutes left : ' => $DiffInMinutes,
    //           ];

    //           $auc['timeLeft'] = $response;
    //           $auc['theOwner'] = $theOwner;
    //           $auc['theOwnerEmail'] = $theOwnerEmail;
    //             if($offeredPrice) {
    //                 $auc['offeredPrice'] = $offeredPrice;
    //             }else{
    //                 $auc['offeredPrice'] = null;
    //             }

    //       }else {
    //           $auc['timeLeft'] = false;
    //             $auc['theOwner'] = $theOwner;
    //             $auc['theOwnerEmail'] = $theOwnerEmail;
    //             if($offeredPrice) {
    //                 $auc['offeredPrice'] = $offeredPrice;
    //             } else{
    //                 $auc['offeredPrice'] = null;
    //             }
    //       }
    //         }

    //         $response = [
    //             'message' => 'auction is founded successfully.',
    //             'auctions' => $auctions,
    //             'status' => true
    //         ];
    //         return response()->json($response);
    //     }else{
    //         $response = [
    //             'message' => 'auction is not  founded successfully.',

    //             'status' => false
    //         ];
    //         return response()->json($response);
    //     }
    // }
    
    
    public function Is_TheUser_In_or_Out_the_Auction(Request $request){

           $auc = Bid::where('profile_id',$request->PID)->pluck('auction_id');
        $collection = collect($auc);
        $auction_ids  = $collection->unique()->values();
        if($auc){
            return response()->json(['auctions'=> $auction_ids]);
        }
        else {
            return response()->json(['auctions'=> 'not found']);
        }
    }
    
 

 }
