<?php

namespace App\Http\Controllers;

use App\Models\HealthCare;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HealthCareController extends Controller
{

    public function getAllHealthCares(){

        $healthcares=HealthCare::all();
        foreach ($healthcares as $healthcare){
            $healthcare->start=Carbon::parse($healthcare->start)->format('H:i');
            $healthcare->end=Carbon::parse($healthcare->end)->format('H:i');
            $healthcare->day = json_decode($healthcare->day);
            $healthcare->user=$healthcare->user;
        }
        return response()->json([
            'HealthCares' => $healthcares,
            'status'=> true
        ]);
    }

    public function getHealthCareByID($id){

        $healthCare = HealthCare::where('id',$id)->first();

        if (!$healthCare) {
            return response()->json([
                'message' => 'Health Care not found!',
                'status' => false
            ]);
        }
        $healthCare->user=$healthCare->user;
        $healthCare->day = json_decode($healthCare->day);
        $healthCare->start=Carbon::parse($healthCare->start)->format('H:i');
        $healthCare->end=Carbon::parse($healthCare->end)->format('H:i');
        return response()->json([
            'Health_Care' => $healthCare,
            'status' => true
        ]);
    }

    public function createHealthCare(Request $request){

        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'mobile' => 'required|max:250',
            'description' => 'required|string|max:250',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'license' => 'required',
            'profile_image' => 'required',
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
        $path = public_path('images/Health_Care/license/');
        $request->license->move($path, $filename);
        $realPath = 'images/Health_Care/license/' . $filename;

        $file_extension = $request->profile_image->getClientOriginalExtension();
        $filename3 = time() . '.' . $file_extension;
        $path = public_path('images/Health_Care/profile_image/');
        $request->profile_image->move($path, $filename3);
        $realPath1 = 'images/Health_Care/profile_image/' . $filename3;

        $user = User::create([
            'mobile' => $request->input('mobile'),
            'password' => bcrypt($request->input('password')),
            'email' => $request->input('email'),
            'type' => $request->input('type'),
            'valid' => 'yes',
        ]);

        $day = json_encode($request->days);
        $healthCare=HealthCare::create([
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
            'profile_image' =>$realPath1
        ]);
        $healthCare->day = json_decode($healthCare->day);
        $data['token'] = $user->createToken($request->email)->plainTextToken;
        $data['user'] = $user;
        $data['health_care'] = $healthCare;
        $user->assignRole('HEALTH');

        return response()->json([
            'message' => 'User is created successfully.',
            'data' => $data,
            'status' => true
        ]);
    }

    public function updateHealthCare(Request $request,$id){

        $healthCare=HealthCare::where('id',$id)->first();
        if (!$healthCare) {
            return response()->json([
                'message' => 'HealthCare not found!',
                'status' => false
            ]);
        }


        if($request->hasFile('license')) {
            $file_extension = $request->license->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/Health_Care/license/');
            $request->license->move($path, $filename);
            $realPath = 'images/Health_Care/license/' . $filename;
            $healthCare->update(['license'=>$realPath]);
        }

        if($request->hasFile('profile_image')) {
            $file_extension = $request->profile_image->getClientOriginalExtension();
            $filename3 = time() . '.' . $file_extension;
            $path = public_path('images/Health_Care/profile_image/');
            $request->profile_image->move($path, $filename3);
            $realPath1 = 'images/Health_Care/profile_image/' . $filename3;
            $healthCare->update(['profile_image'=>$realPath1]);
        }

        $attributes = array_filter($request->all(),function ($value){
            return !is_null($value);
        });

        if (array_key_exists('mobile', $attributes)) {
            $user = User::find($healthCare->user_id);
            $user -> update(['mobile' => $attributes['mobile']]);
        }

        if($request->days)
            $request->days = json_encode($request->days);


        $requestData = collect($attributes)->except(['profile_image','license'])->toArray();
        $healthCare->update($requestData);

        $data['user']= $healthCare->user;

        return response()->json([
            'message' => 'Health Care is updated successfully.',
            'health_care'=>$healthCare,
            'status' => true
        ]);
    }

    public function deleteHealthCare($id){

        $healthCare=HealthCare::where('id',$id)->first();
        $user = User::where('id',$healthCare->user_id)->first();
        if($user) {
            $user->delete();
            return response()->json([
                'message' => 'Health Care was removed successfully.',
                'status' => true
            ]);
        }
        else
            return response()->json([
                'message' => 'Health Care does not exist.',
                'status' => false
            ]);

    }

    public function searchHealthCareByName($name)
    {
        $healthCares = HealthCare::where('name', 'LIKE', '%'. $name . '%')->get();

        if ($healthCares->isEmpty()) {

            return response()->json([
                'message' => 'No Health Cares found.',
                'status' => false
            ]);
        } else {

            foreach ($healthCares as $healthCare) {
                $healthCare->day = json_decode($healthCare->day);
                $healthCare->start = Carbon::parse($healthCare->start)->format('H:i');
                $healthCare->end = Carbon::parse($healthCare->end)->format('H:i');
            }
            return response()->json([
                'message' => 'Health Cares found successfully.',
                'HealthCares' => $healthCares,
                'status' => true
            ]);
        }
    }
}
