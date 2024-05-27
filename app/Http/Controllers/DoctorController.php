<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\HealthCare;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    public function allDoctorsInHeaalthCare($healthCare_id)
    {
        $healthCare = HealthCare::find($healthCare_id);

        if (!$healthCare) {
            return response()->json([
                'message' => 'Health Care not found',
                'status' => false
            ]);
        }

        $doctors = $healthCare->doctors;
        foreach ($doctors as $doctor)
            $doctor->user=$doctor->user;

        return response()->json([
            'Doctors' => $doctors,
            'status' => true
        ]);
    }

    public function getDoctorByID($id){

        $doctor = Doctor::where('id',$id)->first();

        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor not found!',
                'status' => false
            ]);
        }
        $doctor->user=$doctor->user;
        return response()->json([
            'Doctor' => $doctor,
            'status' => true
        ]);
    }

    public function createDoctor(Request $request){

        $validate = Validator::make($request->all(), [
            'firstName' => 'required|string|max:250',
            'mobile' => 'required|max:250',
            'description' => 'required|string|max:250',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'required',
            'birth'=>'required',
            'gender'=>'required',
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
        $path = public_path('images/Doctor/PROFILES/');
        $request->image->move($path, $filename);
        $realPath = 'images/Doctors/PROFILES/'.$filename;

        $user_id = Auth::id();
        $health_care_id = HealthCare::where('user_id',$user_id)->first()->id;
        $user = User::create([
            'mobile' => $request->input('mobile'),
            'password' => bcrypt($request->input('password')),
            'email' => $request->input('email'),
            'type' => $request->input('type'),
            'valid' => 'yes',
        ]);

        $doctor=Doctor::create([
            'user_id' => $user->id,
            'health_care_id'=>$health_care_id,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'birth'=>$request->birth,
            'gender'=>$request->gender,
            'image'=>$realPath,
            'description' => $request->description,
            'experience'=>$request->experience,
            'specialties'=>$request->specialties
        ]);
        $data['token'] = $user->createToken($request->email)->plainTextToken;
        $data['user'] = $user;
        $data['doctor'] = $doctor;
        $user->assignRole('SB');

        return response()->json([
            'message' => 'User is created successfully.',
            'data' => $data,
            'status' => true
        ]);
    }

    public function updateDoctor(Request $request,$id){

        $doctor=Doctor::where('id',$id)->first();
        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor not found!',
                'status' => false
            ]);
        }


        if($request->hasFile('image')) {
            $file_extension = $request->image->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/Doctor/PROFILES/');
            $request->image->move($path, $filename);
            $realPath = 'images/Doctors/PROFILES/'.$filename;
            $doctor->update(['image'=>$realPath]);
        }

        $attributes = array_filter($request->all(),function ($value){
            return !is_null($value);
        });

        if (array_key_exists('mobile', $attributes)) {
            $user = User::find($doctor->user_id);
            $user -> update(['mobile' => $attributes['mobile']]);
        }

        $requestData = collect($attributes)->except(['profile_image','license'])->toArray();
        $doctor->update($requestData);

        $data['user']= $doctor->user;

        return response()->json([
            'message' => 'Doctor is updated successfully.',
            'Doctor'=>$doctor,
            'status' => true
        ]);
    }

    public function deleteDoctor($id){

        $doctor=Doctor::where('id',$id)->first();
        $user = User::where('id',$doctor->user_id)->first();
        if($user) {
            $user->delete();
            return response()->json([
                'message' => 'Doctor was removed successfully.',
                'status' => true
            ]);
        }
        else
            return response()->json([
                'message' => 'Doctor does not exist.',
                'status' => false
            ]);

    }
}
