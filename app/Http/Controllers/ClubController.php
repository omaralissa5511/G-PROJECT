<?php

namespace App\Http\Controllers;
use App\Events\Clubs;
use App\Events\Bids;
use App\Http\Controllers\Controller;
use App\Models\CLUB\ClubImage;
use App\Models\CLUB\Equestrian_club;
use App\Models\CLUB\Trainer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\CLUB\OfferClub;

class ClubController extends Controller
{


    public function getClubByID ($id){

    $club = Equestrian_club::where('id',$id)->first();
        $clubImages = ClubImage::where('club_id', $club->id)->get()->pluck('image_paths')->toArray();
        $club->day = json_decode($club->day);
        $club->day = explode(',', $club->day[0]);
        $response = [

            'club' => $club,
            'images' => $clubImages[0],
            'status' => true
    ];

    return $response;
}

    public function editClub (Request $request){

         $userID = Auth::id();
        $club = Equestrian_club::where('user_id',$userID)->first();

        if($request->hasFile('license')) {
            $file_extension = $request->license->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/Equestrian_club/license/');
            $request->license->move($path, $filename);
            $realPath = 'images/Equestrian_club/license/' . $filename;
            $club->update(['license'=>$realPath]);
        }

        if($request->hasFile('profile')) {
            $file_extension = $request->profile->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/Equestrian_club/profile/');
            $request->profile->move($path, $filename);
            $realPath2 = 'images/Equestrian_club/profile/' . $filename;
            $club->update(['profile'=>$realPath2]);
        }

        if($request->hasFile('images')) {
            $images = $request->file('images');
            $imagePaths = [];
            foreach ($images as $image) {
                $new_name = rand() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/Equestrian_club/club/'), $new_name);
                $imagePaths[] = 'images/Equestrian_club/club/' . $new_name;
                $clubI = ClubImage::where('club_id',$club->id)->first();
                $clubI->update(['image_paths' => $imagePaths]);
            }
        }

        $attributes = array_filter($request->all(),function ($value){
            return !is_null($value);
        });
//   if($request->mobile)
//   {
//       $user = User::find($userID);
//       $user -> update(['mobile' => $attributes['mobile']]);
//   }

         if (array_key_exists('mobile', $attributes)) {
            $user = User::find($userID);
            $user -> update(['mobile' => $attributes['mobile']]);
        }
        $requestData = collect($attributes)->
        except(['images','license','profile','mobile'])->toArray();

        $club->update($requestData);

        $clubImages = ClubImage::where('club_id', $club->id)->get()->pluck('image_paths')->toArray();
        $club = Equestrian_club::where('user_id',$userID)->first();
        $response = [
            'message' => 'User is updated successfully.',
            'club' => $club,
            'clubImages' =>$clubImages,
            'status' => true
        ];
         $message = 'one club have been updated';
        broadcast(new Clubs($message));

        return response()->json($response);
    }


    public function MyClub (){

        $id = Auth::id();
        $club = Equestrian_club::where('user_id',$id)->first();
        if($club) {

            $clubImages = ClubImage::where('club_id', $club->id)->get()->pluck('image_paths')->toArray();
            $club->day = json_decode( $club->day);

            $response = [
                'message' => 'club was found successfully.',
                'club' => $club,
                'images' => $clubImages[0],
                'status' => true
            ];

            return $response;}
        else {
            $response = [
                'message' => 'club does not exist.',
                'status' => false
            ];
            return $response;
        }

    }

    ////////////TRAINER SECTION ///////////
    public function AddTrainer (Request $request){

        $validate = Validator::make($request->all(), [
            'FName' => 'required|string|max:250',
            'mobile' => 'required|max:250',
            'LName' => 'required|string|max:250',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'gender' => 'required',
            'license' => 'required',
            'image' => 'required',
            'qualifications' => 'required',
            'certifications' => 'required',
            'experience' => 'required',
            'specialties' => 'required',
            'address' => 'required',
            'birth'=>'required',
            'days' =>'required',
            'start' => 'required',
            'end' =>'required',
            'images'=>'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }
        $email = $request->email;
        $user = User::where('email', $email)->first();

        if ($user) {
            return response()->json([
                'message' => 'Email already in use.',
                'status' => false
            ]);
        }

        if($request->hasFile('images')){
            $images = $request->file('images');
            $imagePaths = [];
            foreach ($images as $image) {
                $new_name = rand() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/Trainer/'), $new_name);
                $imagePaths[] = 'images/Trainer/' . $new_name;
            }
        }


       if($request->hasFile('image')){
           $file_extension = $request->image->getClientOriginalExtension();
           $filename = time() . '.' . $file_extension;
           $path = public_path('images/Trainer/PROFILES/');
           $request->image->move($path, $filename);
           $realPath = 'images/Trainer/PROFILES/'.$filename;

       }

       if($request->hasFile('license')){
           $file_extension = $request->license->getClientOriginalExtension();
           $filename1 = time() . '.' . $file_extension;
           $path = public_path('images/Trainer/license/');
           $request->license->move($path, $filename1);
           $realPath1 = 'images/Trainer/license/'.$filename1;
       }

        $user = User::create([
            'mobile' => $request->input('mobile'),
            'password' => bcrypt($request->input('password')),
            'email' => $request->input('email'),
            'type' => $request->input('type'),
            'valid' => 'yes',
        ]);
        $currentTime = Carbon::now();

        $user_id = Auth::id();
        $begin = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        $end = $end->format('h:i A');
        $begin = $begin->format('h:i A');
        $days = json_encode($request->days);
        $club_id = Equestrian_club::where('user_id',$user_id)->first()->id;
        $trainer = Trainer::create([
            'user_id' => $user->id,
            'club_id' => $club_id,
            'FName' => $request->FName,
            'lName' => $request->LName,
            'birth' => $request->birth,
            'address' => $request->address,
            'gender' => $request->gender,
            'qualifications' => $request->qualifications,
            'certifications' => $request->certifications,
            'experience' => $request->experience,
            'specialties' => $request->gender,
            'days' => $days,
            'start' => $begin,
            'end' => $end,
            'channelName' => 'testCHANNEL',
            'license' => $realPath1,
            'image' => $realPath,
            'images' =>'$imagePaths'
        ]);
        if($request->hasFile('images')){
            $trainer->images = $imagePaths;
        }

        $channelName =  'trainer_'. $trainer->id;
        $trainer->channelName = $channelName;
        $trainer->save();

        $data['user'] = $user;
        $data['trainer'] = $trainer;


        $user->assignRole('TRAINER');
        $response = [
            'message' => 'User is created successfully.',
            'data' => $data,
            'status' => true
        ];

        $message = 'new trainer have added successfully.';
        Broadcast(new \App\Events\Trainer($message));
        return response()->json($response);
    }

    public function MyTrainers(){

        $user_id = Auth::id();
        $club_id = Equestrian_club::where('user_id',$user_id)->first()->id;
        $trainers = Trainer::where('club_id',$club_id)->get();
        foreach ($trainers as $course){
            $course->days = json_decode($course->days) ;
            $course->days = explode(',', $course->days[0]);
        }
        if($trainers){
            $response = [
                'message' => 'club trainers found : ',
                'trainers' => $trainers,
                'status' => true
            ];
            return $response;
        }else{
            $response = [
                'message' => 'no trainers for you.',
                'status' => false
            ];
            return $response;
        }

    }

    public function GetTrainersByClub($id){


        $trainers = Trainer::where('club_id',$id)->get();
        if($trainers){

            foreach ($trainers as $trainer){
                $trainer->days = json_decode($trainer->days);
                $trainer->days = explode(',', $trainer->days[0]);
                $trainer->images = json_decode($trainer->images);
            }

            $response = [
                'message' => 'club trainers found : ',
                'trainers' => $trainers,
                'status' => true
            ];
            return $response;
        }else{
            $response = [
                'message' => 'no trainers for you.',
                'status' => false
            ];
            return $response;
        }

    }

    public function deleteTrainer ($id){


            $trainer = User::where('id',$id)->first();
            if($trainer) {
                $trainer->delete();
                $response = [
                    'message' => 'trainer was removed successfully.',
                    'status' => true
                ];
                     $message = 'new trainer have added deleted.';
                Broadcast(new \App\Events\Trainer($message));

                return $response;}
            else {
                $response = [
                    'message' => 'trainer does not exist.',
                    'status' => false
                ];
                return $response;
            }

        }
        
      public function Clubs_that_made_offer(){

       $today = Carbon::now();
        $oofer = OfferClub::where('end','>=',$today)->pluck('club_id');
            $collection = collect($oofer);
            $oofer  = $collection->unique();
            $data['clubs'] = $oofer;

        return $data;
        }

}
