<?php

namespace App\Http\Controllers;

use App\Events\Bids;
use App\Events\Clubs;
use App\Events\NotificationE;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\CLUB\Booking;
use App\Models\CLUB\Category;
use App\Models\CLUB\ClubImage;
use App\Models\CLUB\Equestrian_club;
use App\Models\CLUB\Reservation;
use App\Models\CLUB\Trainer;
use App\Models\HealthCare;
use App\Models\Profile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    public function AddClub(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'mobile' => 'required|max:250',
            'description' => 'required|string|max:250',
            'email' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'license' => 'required',
            'images' => 'required',
            'profile' => 'required',
            'website' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'address' => 'required'

        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }


        $file_extension = $request->license->getClientOriginalExtension();
        $filename = time() . '.' . $file_extension;
        $path = public_path('images/Equestrian_club/license/');
        $request->license->move($path, $filename);
        $realPath = 'images/Equestrian_club/license/' . $filename;

        $file_extension = $request->profile->getClientOriginalExtension();
        $filename3 = time() . '.' . $file_extension;
        $path = public_path('images/Equestrian_club/profile/');
        $request->profile->move($path, $filename3);
        $realPath1 = 'images/Equestrian_club/profile/' . $filename3;

        $images = $request->file('images');
        $imagePaths = [];
        foreach ($images as $image) {
            $new_name = rand() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/Equestrian_club/club/'), $new_name);
            $imagePaths[] = 'images/Equestrian_club/club/' . $new_name;
        }
        $user = User::create([
            'mobile' => $request->input('mobile'),
            'password' => bcrypt($request->input('password')),
            'email' => $request->input('email'),
            'type' => $request->input('type'),
            'valid' => 'yes',
        ]);

        $day = json_encode($request->day);
        $club = Equestrian_club::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'long' => $request->long,
            'website' => $request->website,
            'lat' => $request->lat,
            'day' => $day,
            'start' => $request->start,
            'end' => $request->end,
            'license' => $realPath,
            'profile' =>$realPath1
        ]);


        ClubImage::create(['image_paths' => $imagePaths,
            'club_id' => $club->id]);

        $clubImages = ClubImage::where('club_id', $club->id)->get()->pluck('image_paths')->toArray();

        $data['user'] = $user;
        $data['club'] = $club;
        $data['clubImages'] = $clubImages;
        $user->assignRole('CLUB');
        $response = [
            'message' => 'User is created successfully.',
            'data' => $data,
            'status' => true
        ];
        $message = 'one club have been added added';
        broadcast(new Clubs($message));
        return response()->json($response);
    }

    public function getPending_Auctions(){

        $auction = Auction::where('status','pending')->with('profile')->get();
        if($auction){
            $response = [
                'auction' => $auction,
                'status' => true
            ];
            return response()->json($response);
        }
        $response = [
            'message' => 'no pending auctions now',
            'status' => false
        ];
        return response()->json($response);

    }

    public function AuctionApproval(Request $request){

        $AID = $request->id;
        $auction = Auction::find($AID);


        if($request->status == 'confirmed'){
            $auction -> update(['status' => 'confirmed']);
            $auction->save();
                        $message='add new auction';
            Broadcast(new \App\Events\Auction($message));
            $response = [
                'message' => 'CONFIRMED AUCTION ',
                'auction : ' => $auction,
                'status' => true
            ];
            return response()->json($response);
        }
        else {
            $auction -> update(['status' => 'canceled']);
            $auction->save();
            $response = [
                'message' => 'CANCELED AUCTION ',
                'auction : ' => $auction,
                'status' => false
            ];
             $message = 'one auction have been canceled';
                    Broadcast(new \App\Events\Auction($message));
            return response()->json($response);
        }

    }

    public function showClubs()
    {
        $clubs = Equestrian_club::get();

        // foreach ($clubs as $club){
        //     $club->day = json_decode($club->day) ;
        //   //  $club->day = explode(',', $club->day[0]);
        // }
        foreach ($clubs as $club) {
  // Decode the JSON string
  $days = json_decode($club->day);

  // Check if decoding was successful and the result is an array
  if (is_array($days)) {
    // Separate the days using explode()
    $club->day = explode(',', $days[0]); // Access the first element of the array
  } else {
    // Handle potential invalid JSON or decoding failure (optional)
    // You could log an error, set an empty array, or use a default value
    $club->day = []; // Example: set an empty array
  }
}

       
        if ($clubs) {

            $response = [
                'data' => $clubs,
                'status' => true
            ];

            return $response;
        } else {
            $response = [
                'message' => 'there is no club',
                'data' => $clubs,
                'status' => false
            ];
            return $response;
        }

    }
    
     public function showClubsA()
    {
        $clubs = Equestrian_club::get();

        foreach ($clubs as $club){
            $club->day = json_decode($club->day) ;
           // $club->day = explode(',', $club->day[0]);
        }
        if ($clubs) {

            $response = [
                'data' => $clubs,
                'status' => true
            ];

            return $response;
        } else {
            $response = [
                'message' => 'there is no club',
                'data' => $clubs,
                'status' => false
            ];
            return $response;
        }

    }


    public function searchClubByName($name)
    {
        $clubs = Equestrian_club::where('name', 'LIKE','%'. $name . '%')->get();

        if ($clubs->isEmpty()) {

            $response = [
                'message' => 'No clubs found.',
                'status' => false
            ];
        } else {

            foreach ($clubs as $club) {
                $clubImages = ClubImage::where('club_id', $club->id)->pluck('image_paths')->toArray();
                $club->images = $clubImages[0];
            }


            $response = [
                'message' => 'Club(s) found successfully.',
                'clubs' => $clubs,
                'status' => true
            ];
        }

        return $response;
    }

    public function searchClubByID($id)
    {

        $club = Equestrian_club::where('id',$id)->first();

        if (!$club) {
            $response = [
                'message' => 'No club found.',
                'status' => false
            ];
        } else {
            $club->day = json_decode($club->day);
            $club->day = explode(',', $club->day[0]);
            $clubImages = ClubImage::where('club_id', $club->id)->pluck('image_paths')->toArray();

            $response = [
                'message' => 'Club found successfully.',
                'club' => $club,
                'images' => $clubImages,
                'status' => true
            ];
        }

        return $response;
    }




    public function deleteClub($userId)
    {
        $club = User::where('id', $userId)->first();
        if ($club) {
            $club->delete();
            $response = [
                'message' => 'club was deleted successfully.',
                'status' => true
            ];
             $message = 'one club have been deleted';
             broadcast(new Clubs($message));
            return $response;
        } else {
            $response = [
                'message' => 'club does not exist.',
                'status' => false
            ];
            return $response;
        }

    }


//////////////////// HEALTHCARE SECTION //////////////
    public function AddHealthCare(Request $request){

        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'mobile' => 'required|max:250',
            'description' => 'required|string|max:250',
            'email' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'license' => 'required',
            'address' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }
        $file_extension = $request->license->getClientOriginalExtension();
        $filename = time() . '.' . $file_extension;
        $path = public_path('images/USERS/HealthCare/license/');
        $request->license->move($path, $filename);
        $realPath = 'images/USERS/HealthCare/license/' . $filename;

        $user = User::create([
            'mobile' => $request->input('mobile'),
            'password' => bcrypt($request->input('password')),
            'email' => $request->input('email'),
            'type' => $request->input('type'),
            'valid' => 'yes',
        ]);

        $health = HealthCare::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'license' => $realPath,
        ]);

        $data['token'] = $user->createToken($request->email)->plainTextToken;
        $data['user'] = $user;
        $data['healthCare'] = $health;

        $user->assignRole('Admin');
        $response = [
            'message' => 'User is created successfully.',
            'data' => $data,
            'status' => true
        ];

        return response()->json($response);
    }

     public function editHealth_care(Request $request){



         $validate = Validator::make($request->all(), [
             'name' => 'required|string|max:250',
             'mobile' => 'required|max:250',
             'description' => 'required|string|max:250',
             'license' => 'required',
             'address' => 'required'
         ]);

         if ($validate->fails()) {
             return response()->json([
                 'message' => 'Validation Error!',
                 'data' => $validate->errors(),
                 'status' => false
             ]);
         }

         $file_extension = $request->license->getClientOriginalExtension();
         $filename = time() . '.' . $file_extension;
         $path = public_path('images/USERS/license/HealthCare/');
         $request->license->move($path, $filename);
         $realPath = 'images/USERS/license/HealthCare/' . $filename;

         $userID = Auth::id();
         $user = User::find($userID);
         $user -> update(['mobile' => $request->mobile]);

         $health = HealthCare::where('user_id',$userID)->first();
         $health -> update([
             'name' => $request->name,
             'description' => $request->description,
             'address' => $request->address,
             'license' => $filename,
         ]);
         $health = HealthCare::where('user_id',$userID)->first();
         $response = [
             'message' => 'User is updated successfully.',
             'health' => $health,
             'status' => true
         ];

         return response()->json($response);
     }



//////////////////// CATEGORY SECTION ////////////////
    public function createCategory(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name',
            'description' => 'required',
            'image'=>'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }


        $file_extension = $request->image->getClientOriginalExtension();
        $filename = time() . '.' . $file_extension;
        $path = public_path('images/CATEGORY/');
        $request->image->move($path, $filename);
        $realPath = 'images/CATEGORY/'.$filename;

        $category = Category::create([
            'name' => $request->name,
            'description' =>$request->description,
            'image' =>$realPath
        ]);

        $message = 'new category have added successfully.';
        Broadcast(new \App\Events\Category($message));
        return response()->json([
            'message' =>'Category is created successfully.',
            'category' => $category,
            'status' => true
        ]);

    }

    public function getCategories()
    {
        $categories = Category::all();

        return response()->json([
            'categories' => $categories,
            'status'=> true
        ]);
    }

    public function getCategory(Request $request,$id)
    {

        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found!',
                'status' => false
            ]);
        }

        return response()->json([
            'category' => $category,
            'status' => true
        ]);
    }


    public function getCategoryByName($name)
    {

        $category = Category::where('name',$name)->first();


        if (!$category) {
            return response()->json([
                'message' => 'Category not found!',
                'status' => false
            ]);
        }

        return response()->json([
            'category' => $category,
            'status' => true
        ]);
    }


    public function updateCategory(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }


        $category = Category::find($request->id);

        if($request->hasFile('image')) {
            $file_extension = $request->image->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/CATEGORY/');
            $request->image->move($path, $filename);
            $realPath = 'images/CATEGORY/'.$filename;
            $category->update(['image'=>$realPath]);
        }

        $attributes = array_filter($request->all(),function ($value){
            return !is_null($value);
        });
        $requestData = collect($attributes)->
        except(['image','id'])->toArray();

        $category->update($requestData);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found!',
                'status' => false
            ]);
        }
         $message = 'one category have been updated';
        broadcast(new \App\Events\Category($message));

        return response()->json([
            'message' =>'Category is updated successfully.',
            'category' => $category,
            'status' => true
        ]);
        
    }

    public function deleteCategory( Request $request ,$ID)
    {
        $category = Category::find($ID);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found!',
                'status' => false
            ]);
        }

        $category->delete();
          $message = 'one category have been deleted';
        broadcast(new \App\Events\Category($message));

        return response()->json([
            'message' => 'Category is deleted successfully.',
            'status' => true
        ]);
    }

    public function getUserForChart(){
        $users = User::where('type','profile')->get();
        $trainers = User::where('type','Trainer')->get();
        $Equestrian_clubs = User::where('type','Equestrian_club')->get();

     $trainers = sizeof($trainers);
     $users = sizeof($users);
     $Equestrian_clubs = sizeof($Equestrian_clubs);
        return response()->json([
            'users' => $users,
            'trainers' => $trainers,
            'equestrian_clubs' =>$Equestrian_clubs,
            'doctors' => 6,
            'healthCare' => 7
        ]);
    }

    public function getUserDate(){

         $users =  Profile::query()->pluck('created_at','FName');
//        $dataArray = get_object_vars($users);
//         return $dataArray;
//        foreach ($users as $key => $value) {
//            $carbon = Carbon::parse($value);
//            $month = $carbon->format('m'); // Extract month as a numeric string (e.g., 05 for May)
//            $users =  "$key: $month" . PHP_EOL;
//        }
        $monthArray = [];

        foreach ($users as $key => $value) {
            $timestamp = strtotime($value);
            $month = date('m', $timestamp); // Extract month as a numeric string
            $users[$key] = $month; // Add key-value pair to the new array
        }


       // print_r($monthArray);
        return $users;
//        $timestamp = strtotime($users['nemo']);
//        $month = date('m', $timestamp);
//        return $month;
    }


    public function UserInMonth(){
        $users = User::selectRaw('COUNT(*) as user_count, MONTH(created_at) as month')
            ->where('type', 'profile')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        $result = [];
        foreach ($months as $number => $name) {
            $result[$name] = 0;
        }

        foreach ($users as $user) {
            $monthName = $months[$user->month];
            $result[$monthName] = $user->user_count;
        }

        return response()->json([
            'users' => $result,
            'status'=> true
        ]);
    }

    public function AuctionInMonth()
    {
        $auctions = Auction::selectRaw('COUNT(*) as auction_count, MONTH(created_at) as month')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        $result = [];
        foreach ($months as $number => $name) {
            $result[$name] = 0;
        }

        foreach ($auctions as $auction) {
            $monthName = $months[$auction->month];
            $result[$monthName] = $auction->auction_count;
        }

        return response()->json([
            'auctions' => $result,
            'status' => true
        ]);
    }

    public function bookingInMonth($id)
    {
        $bookings = Booking::whereHas('service', function ($query) use ($id) {
            $query->where('club_id', $id);
        })->selectRaw('COUNT(*) as booking_count, MONTH(created_at) as month')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        $result = [];
        foreach ($months as $number => $name) {
            $result[$name] = 0;
        }

        foreach ($bookings as $booking) {
            $monthName = $months[$booking->month];
            $result[$monthName] = $booking->booking_count;
        }

        return response()->json([
            'bookings' => $result,
            'status' => true
        ]);
    }

    public function reservationInMonth($id)
    {
        $reservations = Reservation::whereHas('course', function ($query) use ($id){
            $query->where('club', $id);
        })->selectRaw('COUNT(*) as reservation_count, MONTH(created_at) as month')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        $result = [];
        foreach ($months as $number => $name) {
            $result[$name] = 0;
        }

        foreach ($reservations as $reservation) {
            $monthName = $months[$reservation->month];
            $result[$monthName] = $reservation->reservation_count;
        }

        return response()->json([
            'reservation' => $result,
            'status' => true
        ]);
    }

    public function infoToAdmin(){
        $users=User::where('type','profile')->count();
        $trainers=Trainer::count();
        $clubs=Equestrian_club::count();
        $bookings=Booking::count();
        $reservations=Reservation::count();
        $auctions=Auction::count();

        return response()->json([
            'users' => $users,
            'trainers' => $trainers,
            'clubs' => $clubs,
            'bookings' => $bookings,
            'reservations'=> $reservations,
            'auctions'=> $auctions,
            'status' => true
        ]);
    }
}
