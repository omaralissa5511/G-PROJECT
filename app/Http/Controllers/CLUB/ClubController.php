<?php

namespace App\Http\Controllers\CLUB;

use App\Http\Controllers\Controller;
use App\Models\CLUB\ClubImage;
use App\Models\CLUB\Equestrian_club;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClubController extends Controller
{

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
                'images' => $clubImages,
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


    public function deleteClub (){
        $id = Auth::id();
        $club = User::where('id',$id)->first();
        if($club) {
            $club->delete();
            $response = [
                'message' => 'club was deleted successfully.',
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


}
