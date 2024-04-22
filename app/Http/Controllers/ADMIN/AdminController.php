<?php

namespace App\Http\Controllers\ADMIN;

use App\Http\Controllers\Controller;
use App\Models\CLUB\ClubImage;
use App\Models\CLUB\Equestrian_club;
use App\Models\HealthCare;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{


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
             'license' => $filename,
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



     public function editClub (Request $request){

         $validate = Validator::make($request->all(), [
             'name' => 'required|string|max:250',
             'mobile' => 'required|max:250',
             'description' => 'required|string|max:250',
             'license' => 'required',
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
         $path = public_path('images/USERS/license/Equestrian_club/');
         $request->license->move($path, $filename);



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
             'license' => $filename,
         ]);
         $club = Equestrian_club::where('user_id',$userID)->first();
         $response = [
             'message' => 'User is updated successfully.',
             'club' => $club,
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
}
