<?php

namespace App\Http\Controllers\AUTH;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\CLUB\Equestrian_club;
use App\Models\HealthCare;
use App\Models\Profile;
use App\Models\SellerBuyer;
use App\Models\CLUB\Trainer;
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
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status'=>false
            ]);
        }

        $file_extension = $request->image->getClientOriginalExtension();
        $filename = time() . '.' . $file_extension;
        $path = public_path('images/ADMIN/PROFILES/');
        $request->image->move($path, $filename);
        $realPath = 'images/ADMIN/PROFILES/'.$filename;


        $owner = Admin::create([
            'FName' => $request->FName,
            'LName' => $request->LName,
            'token' => 'ffdfs',
            'mobile' => $request->input('mobile'),
            'password' => bcrypt($request->input('password')),
            'email' => $request->input('email'),
            'image' => $realPath

        ]);

        $data['token'] = $owner->createToken($request->email)->plainTextToken;
        $data['owner'] = $owner;
        $owner -> update(['token'=>$data['token']]);

        $owner->assignRole('ADMIN');

        $response = [
            'message' => 'admin is created successfully.',
            'data' => $data,
            'status' => true
        ];

        return response()->json($response);

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
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }

        $file_extension = $request->image->getClientOriginalExtension();
        $filename = time() . '.' . $file_extension;
        $path = public_path('images/ADMIN/PROFILES/');
        $request->image->move($path, $filename);
        $realPath = 'images/ADMIN/PROFILES/'.$filename;


        $token_fromRequest =  $request->bearerToken();
        $admin = Admin::where('token',$token_fromRequest)->first();


        $admin -> update([
            'FName' => $request->FName,
            'LName' => $request->LName,
            'mobile' => $request->input('mobile'),
            'image' => $realPath
        ]);
        $admin = Admin::where('token',$token_fromRequest)->first();

        $response = [
            'message' => 'admin is updated successfully.',
            'admin' => $admin,
            'status' => true,
        ];

        return response()->json($response);

    }



    public function register(Request $request)
    {

        if ($request->type == 'profile') {

            $validate = Validator::make($request->all(), [
                'FName' => 'required|string|max:250',
                'mobile' => 'required|max:250',
                'LName' => 'required|string|max:250',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'gender' => 'required',
                'birth' => 'required',
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
            $path = public_path('images/USERS/PROFILES/');
            $request->image->move($path, $filename);
            $realPath = 'images/USERS/PROFILES/'.$filename;



            $user = User::create([
                'mobile' => $request->input('mobile'),
                'password' => bcrypt($request->input('password')),
                'email' => $request->input('email'),
                'type' => $request->input('type'),
                'valid' => 'yes',
            ]);

            $profile = Profile::create([
                'user_id' => $user->id,
                'FName' => $request->FName,
                'LName' => $request->LName,
                'birth' => $request->birth,
                'address' => $request->address,
                'gender' => $request->gender,
                'profile' => $realPath
            ]);

            $data['token'] = $user->createToken($request->email)->plainTextToken;
            $data['user'] = $user;
            $data['profile'] = $profile;
            $user->assignRole('USER');


            //event(new NewUSERAdded($user));

            $response = [
                'message' => 'User is created successfully.',
                'data' => $data,
                'status' => true
            ];

            return response()->json($response);
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
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                    'status' => false
                ]);
            }


            $file_extension = $request->license->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/USERS/license/SELLER-BUYER/');
            $request->license->move($path, $filename);
            $realPath = 'images/USERS/license/SELLER-BUYER/'.$filename;


            $file_extension = $request->image->getClientOriginalExtension();
            $filename1 = time() . '.' . $file_extension;
            $path = public_path('images/USERS/PROFILES/SELLER-BUYER/');
            $request->image->move($path, $filename1);
            $realPath1 = 'images/USERS/PROFILES/SELLER-BUYER/'.$filename1;


            $user = User::create([
                'mobile' => $request->input('mobile'),
                'password' => bcrypt($request->input('password')),
                'email' => $request->input('email'),
                'type' => $request->input('type'),
                'valid' => 'yes',
            ]);

            $SB = SellerBuyer::create([
                'user_id' => $user->id,
                'FName' => $request->FName,
                'lName' => $request->LName,
                'birth' => $request->birth,
                'address' => $request->address,
                'gender' => $request->gender,
                'license' => $realPath,
                'image' => $realPath1
            ]);

            $data['token'] = $user->createToken($request->email)->plainTextToken;
            $data['user'] = $user;
            $data['SB'] = $SB;


            $user->assignRole('SB');

            $response = [
                'message' => 'User is created successfully.',
                'data' => $data,
                'status' => true
            ];

            return response()->json($response);
        }




        if ($request->type == 'HealthCare') {

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
            $realPath = 'images/USERS/license/HealthCare/'.$filename;




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


            $user->assignRole('HEALTH');

            $response = [
                'message' => 'User is created successfully.',
                'data' => $data,
                'status' => true
            ];

            return response()->json($response);
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
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                    'status' => false
                ]);
            }

            $file_extension = $request->image->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/USERS/PROFILES/');
            $request->image->move($path, $filename);
            $realPath = 'images/USERS/PROFILES/'.$filename;


            $userID = Auth::id();
            $user = User::find($userID);
            $user -> update(['mobile' => $request->mobile]);


            $profile = Profile::where('user_id',$userID)->first();
            $profile -> update([
                'FName' => $request->FName,
                'LName' => $request->LName,
                'address' => $request->address,
                'profile' => $realPath
            ]);
            $profile = Profile::where('user_id',$userID)->first();
            $response = [
                'message' => 'User is updated successfully.',
                'profile' => $profile,
                'status' => true
            ];

            return response()->json($response);
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
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                    'status' => false
                ]);
            }

            $file_extension = $request->license->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/USERS/license/SELLER-BUYER/');
            $request->license->move($path, $filename);
            $realPath = 'images/USERS/license/SELLER-BUYER/'.$filename;


            $file_extension = $request->image->getClientOriginalExtension();
            $filename1 = time() . '.' . $file_extension;
            $path = public_path('images/USERS/PROFILES/SELLER-BUYER/');
            $request->image->move($path, $filename1);

            $realPath1 = 'images/USERS/PROFILES/SELLER-BUYER/'.$filename1;


            $userID = Auth::id();
            $user = User::find($userID);
            $user -> update(['mobile' => $request->mobile]);


            $SB = SellerBuyer::where('user_id',$userID)->first();
            $SB -> update([
                'FName' => $request->FName,
                'LName' => $request->LName,
                'address' => $request->address,
                'license' => $realPath,

                'image' => $realPath1

            ]);
            $SB = SellerBuyer::where('user_id',$userID)->first();
            $response = [
                'message' => 'User is updated successfully.',
                'SB' => $SB,
                'status' => true
            ];

            return response()->json($response);
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
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                    'status' =>false
                ]);
            }

            $file_extension = $request->license->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/USERS/license/Equestrian_club/');
            $request->license->move($path, $filename);

            $realPath = 'images/USERS/license/Equestrian_club/'.$filename;


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
            $club = Equestrian_club::where('user_id',$userID)->first();
            $response = [
                'message' => 'User is updated successfully.',
                'club' => $club,
                'status' => true
            ];

            return response()->json($response);
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
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                    'status' => false
                ]);
            }

            $file_extension = $request->license->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/USERS/license/HealthCare/');
            $request->license->move($path, $filename);

            $realPath = 'images/USERS/license/HealthCare/'.$filename;


            $userID = Auth::id();
            $user = User::find($userID);
            $user -> update(['mobile' => $request->mobile]);

            $health = HealthCare::where('user_id',$userID)->first();
                $health -> update([
                'name' => $request->name,
                'description' => $request->description,
                'address' => $request->address,
                'license' => $realPath,
            ]);
            $health = HealthCare::where('user_id',$userID)->first();
            $response = [
                'message' => 'User is updated successfully.',
                'health' => $health,
                'status' => true
            ];

            return response()->json($response);
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
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                    'status' => false
                ]);
            }

            $file_extension = $request->image->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/USERS/PROFILES/Trainer/');
            $request->image->move($path, $filename);

            $realPath = 'images/USERS/PROFILES/Trainer/'.$filename;


            $file_extension = $request->license->getClientOriginalExtension();
            $filename1 = time() . '.' . $file_extension;
            $path = public_path('images/USERS/license/Trainer/');
            $request->license->move($path, $filename1);

            $realPath1 = 'images/USERS/license/Trainer/'.$filename1;


            $userID = Auth::id();
            $user = User::find($userID);
            $user -> update(['mobile' => $request->mobile]);

            $trainer = Trainer::where('user_id',$userID)->first();

            $trainer -> update([
                'club_id' => $request->club_id,
                'FName' => $request->FName,
                'LName' => $request->LName,
                'address' => $request->address,
                'license' => $realPath1,
                'image' => $realPath
            ]);
            $trainer = Trainer::where('user_id',$userID)->first();

            $response = [
                'message' => 'TRAINER is UPDATED successfully.',
                'trainer' => $trainer,
                'status' => true
            ];

            return response()->json($response);
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
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }

        $user = User::where('email', $request->email)->first();
        $admin = Admin::where('email', $request->email)->first();
        if($user){
            if(!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                    'status' => false
                ]);
            }

            if($user->type == 'Trainer'){
                $user = User::where('email', $request->email)
                    ->with('trainers')->first();
            }

            elseif ($user->type == 'profile'){
                if (!$user->email_verified_at) {
                    return response()->json([
                        'message' => 'Please verify your email address to login.',
                        'status' => false
                    ]);
                }
                $user = User::where('email', $request->email)
                    ->with('profiles')->first();
            }
            elseif ($user->type == 'Equestrian_club'){
                $user = User::where('email', $request->email)
                    ->with('equestrian_club')->first();
            }
            elseif ($user->type == 'HealthCare'){
            $user = User::where('email', $request->email)
                ->with('health_care')->first();

            }
              elseif ($user->type == 'Doctor'){
            $user = User::where('email', $request->email)
                ->with('doctor')->first();

            }
            // else{
            //     $user = User::where('email', $request->email)
            //         ->with('doctor')->first();
            // }
            $data['token'] = $user->createToken($request->email)->plainTextToken;
            $data['user'] = $user;
            $role =  $user->roles;

            $response = [
                'message' => 'User is logged in successfully.',
                'data' => $data,
                'role' => $role[0]->name,
                'status' => true
            ];
            return response()->json($response);

        }
        elseif ($admin){
            if(!$admin || !Hash::check($request->password, $admin->password)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                    'status' => false
                ]);
            }

            $data['token'] = $admin->createToken($request->email)->plainTextToken;
            $data['admin'] = $admin;
            $role =  $admin->roles;

            $admin = Admin::where('email', $request->email)->first();
            $admin->update(['token' => $data['token']]);

            $response = [
                'message' => 'admin is logged in successfully.',
                'data' => $data,
                'role' => $role[0]->name,
                'status' => true
            ];

            return response()->json($response);
        }   else{
            $response = [
                'message' => 'this email does not exist',
                'status' => false
            ];
            return response()->json($response);
        }

    }



    public function AdminLogin(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if($validate->fails()){
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }

        $admin = Admin::where('email', $request->email)->first();

        // Check password
        if(!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
                'status' => false
            ]);
        }

        $data['token'] = $admin->createToken($request->email)->plainTextToken;
        $data['admin'] = $admin;
        $role =  $admin->roles;

        $admin = Admin::where('email', $request->email)->first();
        $admin->update(['token' => $data['token']]);

        $response = [
            'message' => 'admin is logged in successfully.',
            'data' => $data,
            'role' => $role[0]->name,
            'status' => true
        ];

        return response()->json($response);
    }



    public function testontoken(Request $request){


        $token_fromRequest =  $request->bearerToken();
        $token_fromDB = Admin::where('token',$token_fromRequest)
            ->pluck('token');


    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'User is logged out successfully.',
            'status' => true
        ]);
    }
}
