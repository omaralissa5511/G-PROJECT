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
            'mobile' => $request->input('mobile'),
            'password' => bcrypt($request->input('password')),
            'email' => $request->input('email'),
            'image' => $filename

        ]);

        $data['token'] = $owner->createToken($request->email)->plainTextToken;
        $data['owner'] = $owner;

        $owner->assignRole('Super Admin');

        $response = [
            'status' => 'success',
            'message' => 'admin is created successfully.',
            'data' => $data,
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



//            $license = $request->file('image');
//            $destinationPathImg = public_path('uploads/licenses/');
//            if (!$license->move($destinationPathImg, $license->getClientOriginalName())) {
//                return 'Error saving the file.';
//            }

//            $file = $request->file('image') ;
//            $fileName = $file->getClientOriginalName() ;
//            $destinationPath = public_path().'/images' ;
//            $file->move($destinationPath,$fileName);



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
                'LName' => $request->lName,
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

        if ($request->type == 'Equestrian_club') {

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
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
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
            'status' => 'success',
            'message' => 'User is created successfully.',
            'data' => $data,
        ];

        return response()->json($response, 201);
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
                    'status' => 'failed',
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                ], 403);
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

        // Check email exist
        $user = User::where('email', $request->email)->first();

        // Check password
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

        // Check email exist
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

        $response = [
            'status' => 'success',
            'message' => 'admin is logged in successfully.',
            'data' => $data,
        ];

        return response()->json($response, 200);
    }

}
