<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CLUB\ClubImage;
use App\Models\CLUB\Equestrian_club;
use App\Models\CLUB\Trainer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClubController extends Controller
{


    public function getClubByID ($id){

    $club = Equestrian_club::where('id',$id)->first();
        $clubImages = ClubImage::where('club_id', $club->id)->get()->pluck('image_paths')->toArray();

        $response = [

            'club' => $club,
            'images' => $clubImages[0],
            'status' => true
    ];

    return $response;
}

    public function editClub (Request $request){

        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'mobile' => 'required|max:250',
            'description' => 'required|string|max:250',
            'license' => 'required',
            'images' => 'required',
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

        $images = $request->file('images');
        $imagePaths = [];
        foreach ($images as $image) {
            $new_name = rand() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/Equestrian_club/club/'), $new_name);
            $imagePaths[] = 'images/Equestrian_club/club/' . $new_name;
        }
        $userID = Auth::id();
        $user = User::find($userID);
        $user -> update(['mobile' => $request->mobile]);

        $club = Equestrian_club::where('user_id',$userID)->first();
        $club -> update([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'long' => $request->long,
            'lat' => $request->lat,
            'license' => $realPath,
        ]);


        $clubI = ClubImage::where('club_id',$club->id)->first();
        $clubI->update(['image_paths' => $imagePaths]);
        $clubImages = ClubImage::where('club_id', $club->id)->get()->pluck('image_paths')->toArray();
        $club = Equestrian_club::where('user_id',$userID)->first();
        $response = [
            'message' => 'User is updated successfully.',
            'club' => $club,
            'clubImages' =>$clubImages,
            'status' => true
        ];

        return response()->json($response);
    }

    public function MyClub (){

        $id = Auth::id();
        $club = Equestrian_club::where('user_id',$id)->first();
        if($club) {

            $clubImages = ClubImage::where('club_id', $club->id)->get()->pluck('image_paths')->toArray();

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
            'email' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'gender' => 'required',
            'license' => 'required',
            'image' => 'required',
            'qualifications' => 'required',
            'certifications' => 'required',
            'experience' => 'required',
            'specialties' => 'required',
            'address' => 'required'
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
        $path = public_path('images/Trainer/PROFILES/');
        $request->image->move($path, $filename);
        $realPath = 'images/Trainer/PROFILES/'.$filename;

        $file_extension = $request->license->getClientOriginalExtension();
        $filename1 = time() . '.' . $file_extension;
        $path = public_path('images/Trainer/license/');
        $request->license->move($path, $filename1);
        $realPath1 = 'images/Trainer/license/'.$filename1;

        $user = User::create([
            'mobile' => $request->input('mobile'),
            'password' => bcrypt($request->input('password')),
            'email' => $request->input('email'),
            'type' => $request->input('type'),
            'valid' => 'yes',
        ]);
           $user_id = Auth::id();
           $club_id = Equestrian_club::where('user_id',$user_id)->first()->id;
        $trainer = Trainer::create([
            'user_id' => $user->id,
            'club_id' => $club_id,
            'FName' => $request->FName,
            'LName' => $request->LName,
            'birth' => $request->birth,
            'address' => $request->address,
            'gender' => $request->gender,
            'qualifications' => $request->qualifications,
            'certifications' => $request->certifications,
            'experience' => $request->experience,
            'specialties' => $request->gender,
            'license' => $realPath1,
            'image' => $realPath
        ]);

        $data['user'] = $user;
        $data['trainer'] = $trainer;

        $user->assignRole('TRAINER');
        $response = [
            'message' => 'User is created successfully.',
            'data' => $data,
            'status' => true
        ];

        return response()->json($response);
    }

    public function MyTrainers(){

        $user_id = Auth::id();
        $club_id = Equestrian_club::where('user_id',$user_id)->first()->id;
        $trainers = Trainer::where('club_id',$club_id)->get();
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

                return $response;}
            else {
                $response = [
                    'message' => 'trainer does not exist.',
                    'status' => false
                ];
                return $response;
            }

        }

}
