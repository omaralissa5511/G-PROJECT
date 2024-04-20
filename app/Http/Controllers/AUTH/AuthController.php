<?php

namespace App\Http\Controllers\AUTH;

use App\Http\Controllers\Controller;
use App\Models\AdminModel;
use App\Models\Equestrian_clubModel;
use App\Models\HealthCareModel;
use App\Models\ProfileModel;
use App\Models\SellerBuyerModel;
use App\Models\TrainerModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{


    public function AdminRegister(Request $request){

        $validate = Validator::make($request->all(), [
            'FName' => 'required|string|max:250',
            'mobile' => 'required|max:250',
            'LName' => 'required|string|max:250',
            'email' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'required',

        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $file_extension = $request->image->getClientOriginalExtension();
        $filename = time() . '.' . $file_extension;
        $path = public_path('images/ADMIN/PROFILES');
        $request->image->move($path, $filename);

        $owner = AdminModel::create([
            'FName' => $request->FName,
            'LName' => $request->LName,
            'token' => 'ffdfs',
            'mobile' => $request->input('mobile'),
            'password' => bcrypt($request->input('password')),
            'email' => $request->input('email'),
            'image' => $filename

        ]);

        $data['token'] = $owner->createToken($request->email)->plainTextToken;
        $data['owner'] = $owner;
        $owner -> update(['token'=>$data['token']]);

        $owner->assignRole('Super Admin');

        $response = [
            'status' => 'success',
            'message' => 'admin is created successfully.',
            'data' => $data,
        ];

        return response()->json($response, 201);

    }


    public function AdminUpdate(Request $request){

        $validate = Validator::make($request->all(), [
            'FName' => 'required|string|max:250',
            'mobile' => 'required|max:250',
            'LName' => 'required|string|max:250',
            'image' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $file_extension = $request->image->getClientOriginalExtension();
        $filename = time() . '.' . $file_extension;
        $path = public_path('images/ADMIN/PROFILES');
        $request->image->move($path, $filename);

        $token_fromRequest =  $request->bearerToken();
        $admin = AdminModel::where('token',$token_fromRequest)->first();


        $admin -> update([
            'FName' => $request->FName,
            'LName' => $request->LName,
            'mobile' => $request->input('mobile'),
            'image' => $filename
        ]);
        $admin = AdminModel::where('token',$token_fromRequest)->first();

        $response = [
            'status' => 'success',
            'message' => 'admin is updated successfully.',
            'admin' => $admin
        ];

        return response()->json($response, 201);

    }



    public function register(Request $request)
    {

        if ($request->type == 'profile') {

            $validate = Validator::make($request->all(), [
                'FName' => 'required|string|max:250',
                'mobile' => 'required|max:250',
                'LName' => 'required|string|max:250',
                'email' => 'required',
                'password' => 'required|string|min:8|confirmed',
                'gender' => 'required',
                'birth' => 'required',
                'address' => 'required'
            ]);


            if ($validate->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                ], 403);
            }

            $file_extension = $request->image->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/USERS/PROFILES');
            $request->image->move($path, $filename);


            $user = User::create([
                'mobile' => $request->input('mobile'),
                'password' => bcrypt($request->input('password')),
                'email' => $request->input('email'),
                'type' => $request->input('type'),
                'valid' => 'yes',
            ]);

            $profile = ProfileModel::create([
                'user_id' => $user->id,
                'FName' => $request->FName,
                'LName' => $request->LName,
                'birth' => $request->birth,
                'address' => $request->address,
                'gender' => $request->gender,
                'image' => $filename

            ]);

            $data['token'] = $user->createToken($request->email)->plainTextToken;
            $data['user'] = $user;
            $data['profile'] = $profile;


            $user->assignRole('Normal User');

            $response = [
                'status' => 'success',
                'message' => 'User is created successfully.',
                'data' => $data,
            ];

            return response()->json($response, 201);
        }


        if ($request->type == 'Seller-Buyer') {

            $validate = Validator::make($request->all(), [
                'FName' => 'required|string|max:250',
                'mobile' => 'required|max:250',
                'LName' => 'required|string|max:250',
                'email' => 'required',
                'password' => 'required|string|min:8|confirmed',
                'license' => 'required',
                'image' => 'required',
                'birth' => 'required',
                'address' => 'required'
            ]);


            if ($validate->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                ], 403);
            }


            $file_extension = $request->license->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/USERS/license/SELLER-BUYER');
            $request->license->move($path, $filename);

            $file_extension = $request->image->getClientOriginalExtension();
            $filename1 = time() . '.' . $file_extension;
            $path = public_path('images/USERS/PROFILES/SELLER-BUYER/');
            $request->image->move($path, $filename1);


            $user = User::create([
                'mobile' => $request->input('mobile'),
                'password' => bcrypt($request->input('password')),
                'email' => $request->input('email'),
                'type' => $request->input('type'),
                'valid' => 'yes',
            ]);

            $SB = SellerBuyerModel::create([
                'user_id' => $user->id,
                'FName' => $request->FName,
                'lName' => $request->LName,
                'birth' => $request->birth,
                'address' => $request->address,
                'gender' => $request->gender,
                'license' => $filename,
                'image' => $filename1
            ]);

            $data['token'] = $user->createToken($request->email)->plainTextToken;
            $data['user'] = $user;
            $data['SB'] = $SB;


            $user->assignRole('Admin');

            $response = [
                'status' => 'success',
                'message' => 'User is created successfully.',
                'data' => $data,
            ];

            return response()->json($response, 201);
        }

        if ($request->type == 'Trainer') {

            $validate = Validator::make($request->all(), [
                'FName' => 'required|string|max:250',
                'mobile' => 'required|max:250',
                'LName' => 'required|string|max:250',
                'email' => 'required',
                'password' => 'required|string|min:8|confirmed',
                'gender' => 'required',
                'license' => 'required',
                'image' => 'required',
                'birth' => 'required',
                'address' => 'required'
            ]);


            if ($validate->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                ], 403);
            }

            $file_extension = $request->image->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/USERS/PROFILES/Trainer');
            $request->image->move($path, $filename);

            $file_extension = $request->license->getClientOriginalExtension();
            $filename1 = time() . '.' . $file_extension;
            $path = public_path('images/USERS/license/Trainer');
            $request->license->move($path, $filename1);

            $user = User::create([
                'mobile' => $request->input('mobile'),
                'password' => bcrypt($request->input('password')),
                'email' => $request->input('email'),
                'type' => $request->input('type'),
                'valid' => 'yes',
            ]);

            $trainer = TrainerModel::create([
                'user_id' => $user->id,
                'club_id' => $request->club_id,
                'FName' => $request->FName,
                'LName' => $request->LName,
                'birth' => $request->birth,
                'address' => $request->address,
                'gender' => $request->gender,
                'license' => $filename1,
                'image' => $filename
            ]);

            $data['token'] = $user->createToken($request->email)->plainTextToken;
            $data['user'] = $user;
            $data['trainer'] = $trainer;


            $user->assignRole('Admin');

            $response = [
                'status' => 'success',
                'message' => 'User is created successfully.',
                'data' => $data,
            ];

            return response()->json($response, 201);
        }


    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */




    public function update(Request $request)
    {

        if ($request->type == 'profile') {

            $validate = Validator::make($request->all(), [
                'FName' => 'required|string|max:250',
                'mobile' => 'required|max:250',
                'LName' => 'required|string|max:250',
                'address' => 'required',
                 'image' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                ], 403);
            }

            $file_extension = $request->image->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/USERS/PROFILES');
            $request->image->move($path, $filename);

            $userID = Auth::id();
            $user = User::find($userID);
            $user -> update(['mobile' => $request->mobile]);


            $profile = ProfileModel::where('user_id',$userID)->first();
            $profile -> update([
                'FName' => $request->FName,
                'LName' => $request->LName,
                'address' => $request->address,
                'image' => $filename
            ]);
            $profile = ProfileModel::where('user_id',$userID)->first();
            $response = [
                'status' => 'success',
                'message' => 'User is updated successfully.',
                'profile' => $profile
            ];

            return response()->json($response, 201);
        }

        if ($request->type == 'Seller-Buyer') {

            $validate = Validator::make($request->all(), [
                'FName' => 'required|string|max:250',
                'mobile' => 'required|max:250',
                'LName' => 'required|string|max:250',
                'license' => 'required',
                'address' => 'required',
                'image' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                ], 403);
            }

            $file_extension = $request->license->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/USERS/license/SELLER-BUYER');
            $request->license->move($path, $filename);

            $file_extension = $request->image->getClientOriginalExtension();
            $filename1 = time() . '.' . $file_extension;
            $path = public_path('images/USERS/PROFILES/SELLER-BUYER/');
            $request->image->move($path, $filename1);

            $userID = Auth::id();
            $user = User::find($userID);
            $user -> update(['mobile' => $request->mobile]);


            $SB = SellerBuyerModel::where('user_id',$userID)->first();
            $SB -> update([
                'FName' => $request->FName,
                'LName' => $request->LName,
                'address' => $request->address,
                'license' => $filename,
                'image' => $filename1
            ]);
            $SB = SellerBuyerModel::where('user_id',$userID)->first();
            $response = [
                'status' => 'success',
                'message' => 'User is updated successfully.',
                'SB' => $SB
            ];

            return response()->json($response, 201);
        }

        if ($request->type == 'Equestrian_club') {

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
                    'status' => 'failed',
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                ], 403);
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
                'status' => 'success',
                'message' => 'User is updated successfully.',
                'club' => $club
            ];

            return response()->json($response, 201);
        }



        if ($request->type == 'HealthCare') {

            $validate = Validator::make($request->all(), [
                'name' => 'required|string|max:250',
                'mobile' => 'required|max:250',
                'description' => 'required|string|max:250',
                'license' => 'required',
                'address' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                ], 403);
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
                'status' => 'success',
                'message' => 'User is updated successfully.',
                'health' => $health
            ];

            return response()->json($response, 201);
        }

        if ($request->type == 'Trainer') {

            $validate = Validator::make($request->all(), [
                'FName' => 'required|string|max:250',
                'mobile' => 'required|max:250',
                'LName' => 'required|string|max:250',
                'license' => 'required',
                'image' => 'required',
                'address' => 'required',
                'club_id' => 'required'
            ]);
            if ($validate->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                ], 403);
            }

            $file_extension = $request->image->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/USERS/PROFILES/Trainer');
            $request->image->move($path, $filename);

            $file_extension = $request->license->getClientOriginalExtension();
            $filename1 = time() . '.' . $file_extension;
            $path = public_path('images/USERS/license/Trainer');
            $request->license->move($path, $filename1);

            $userID = Auth::id();
            $user = User::find($userID);
            $user -> update(['mobile' => $request->mobile]);

            $trainer = TrainerModel::where('user_id',$userID)->first();

            $trainer -> update([
                'club_id' => $request->club_id,
                'FName' => $request->FName,
                'LName' => $request->LName,
                'address' => $request->address,
                'license' => $filename1,
                'image' => $filename
            ]);
            $trainer = TrainerModel::where('user_id',$userID)->first();

            $response = [
                'status' => 'success',
                'message' => 'TRAINER is UPDATED successfully.',
                'trainer' => $trainer
            ];

            return response()->json($response, 201);
        }


    }



    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }


        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid credentials'
            ], 401);
        }

        $data['token'] = $user->createToken($request->email)->plainTextToken;
        $data['user'] = $user;

        $response = [
            'status' => 'success',
            'message' => 'User is logged in successfully.',
            'data' => $data,
        ];

        return response()->json($response, 200);
    }



    public function AdminLogin(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $admin = AdminModel::where('email', $request->email)->first();

        // Check password
        if(!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid credentials'
            ], 401);
        }

        $data['token'] = $admin->createToken($request->email)->plainTextToken;
        $data['admin'] = $admin;

        $admin = AdminModel::where('email', $request->email)->first();
        $admin->update(['token' => $data['token']]);

        $response = [
            'status' => 'success',
            'message' => 'admin is logged in successfully.',
            'data' => $data,
        ];

        return response()->json($response, 200);
    }



    public function testontoken(Request $request){


        $token_fromRequest =  $request->bearerToken();
        $token_fromDB = AdminModel::where('token',$token_fromRequest)
            ->pluck('token');


    }


}
