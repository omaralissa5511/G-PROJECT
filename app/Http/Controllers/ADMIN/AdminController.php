<?php

namespace App\Http\Controllers\ADMIN;

use App\Http\Controllers\Controller;
use App\Models\Equestrian_clubModel;
use App\Models\HealthCareModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
         public function AddClub (Request $request){


             $validate = Validator::make($request->all(), [
                 'name' => 'required|string|max:250',
                 'mobile' => 'required|max:250',
                 'description' => 'required|string|max:250',
                 'email' => 'required',
                 'password' => 'required|string|min:8|confirmed',
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

             $user = User::create([
                 'mobile' => $request->input('mobile'),
                 'password' => bcrypt($request->input('password')),
                 'email' => $request->input('email'),
                 'type' => $request->input('type'),
                 'valid' => 'yes',
             ]);

             $club = Equestrian_clubModel::create([
                 'user_id' => $user->id,
                 'name' => $request->name,
                 'description' => $request->description,
                 'address' => $request->address,
                 'long' => $request->long,
                 'lat' => $request->lat,
                 'license' => $filename,
             ]);

             $data['token'] = $user->createToken($request->email)->plainTextToken;
             $data['user'] = $user;
             $data['club'] = $club;

             $user->assignRole('Admin');

             $response = [
                 'message' => 'User is created successfully.',
                 'data' => $data,
                 'status' => true
             ];

             return response()->json($response);
     }


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
         $path = public_path('images/USERS/license/HealthCare/');
         $request->license->move($path, $filename);

         $user = User::create([
             'mobile' => $request->input('mobile'),
             'password' => bcrypt($request->input('password')),
             'email' => $request->input('email'),
             'type' => $request->input('type'),
             'valid' => 'yes',
         ]);

         $health = HealthCareModel::create([
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

         $club = Equestrian_clubModel::where('user_id',$userID)->first();
         $club -> update([
             'name' => $request->name,
             'description' => $request->description,
             'address' => $request->address,
             'long' => $request->long,
             'lat' => $request->lat,
             'license' => $filename,
         ]);
         $club = Equestrian_clubModel::where('user_id',$userID)->first();
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

         $health = HealthCareModel::where('user_id',$userID)->first();
         $health -> update([
             'name' => $request->name,
             'description' => $request->description,
             'address' => $request->address,
             'license' => $filename,
         ]);
         $health = HealthCareModel::where('user_id',$userID)->first();
         $response = [
             'message' => 'User is updated successfully.',
             'health' => $health,
             'status' => true
         ];

         return response()->json($response);
     }
}
