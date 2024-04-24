<?php

namespace App\Http\Controllers;

use App\Models\CLUB\ClubImage;
use App\Models\CLUB\Equestrian_club;
use App\Models\CLUB\Trainer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TrainerController extends Controller
{

    public function editTrainer (Request $request){

        $validate = Validator::make($request->all(), [
            'FName' => 'required|string|max:250',
            'mobile' => 'required|max:250',
            'LName' => 'required|string|max:250',
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

        $userID = Auth::id();
        $user = User::find($userID)->first();
        $user -> update(['mobile' => $request->input('mobile'),]);

        $trainer = Trainer::where('user_id',$userID)->first();
        $trainer -> update([

            'FName' => $request->FName,
            'LName' => $request->LName,
            'address' => $request->address,
            'qualifications' => $request->qualifications,
            'certifications' => $request->certifications,
            'experience' => $request->experience,
            'specialties' => $request->gender,
            'license' => $realPath1,
            'image' => $realPath
        ]);

        $trainer = Trainer::where('user_id',$userID)->first();
        $data['user'] = $user;
        $data['trainer'] = $trainer;

        $response = [
            'message' => 'profile is updated successfully.',
            'data' => $data,
            'status' => true
        ];

        return response()->json($response);
    }


    public function MyProfile (){

        $id = Auth::id();
        $trainer = Trainer::where('user_id',$id)->first();

            $response = [

                'trainer' => $trainer,
                'status' => true
            ];

            return $response;
        }


    public function getTrainerByID ($id){

        $trainer = Trainer::where('id',$id)->first();

        $response = [

            'trainer' => $trainer,
            'status' => true
        ];

        return $response;
    }

}


