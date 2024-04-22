<?php

namespace App\Http\Controllers\ADMIN;

use App\Http\Controllers\Controller;
use App\Models\CLUB\ClubImage;
use App\Models\CLUB\Equestrian_club;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AClubController extends Controller
{
    public function AddClub(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'mobile' => 'required|max:250',
            'description' => 'required|string|max:250',
            'email' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'license' => 'required',
            'images' => 'required',
            'website' => 'required',
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
        $user = User::create([
            'mobile' => $request->input('mobile'),
            'password' => bcrypt($request->input('password')),
            'email' => $request->input('email'),
            'type' => $request->input('type'),
            'valid' => 'yes',
        ]);
        $club = Equestrian_club::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'long' => $request->long,
            'website' => $request->website,
            'lat' => $request->lat,
            'license' => $realPath
        ]);


        ClubImage::create(['image_paths' => $imagePaths,
            'club_id' => $club->id]);

        $clubImages = ClubImage::where('club_id', $club->id)->get()->pluck('image_paths')->toArray();

        $data['user'] = $user;
        $data['club'] = $club;
        $data['clubImages'] = $clubImages;
        $user->assignRole('Admin');
        $response = [
            'message' => 'User is created successfully.',
            'data' => $data,
            'status' => true
        ];
        return response()->json($response);
    }

    public function showClub()
    {
        $clubs = Equestrian_club::get();
        if ($clubs) {
            $response = [
                'data' => $clubs,
                'status' => true
            ];

            return $response;
        } else {
            $response = [
                'message' => 'there is no club',
                'data' => $clubs,
                'status' => false
            ];
            return $response;
        }

    }

    public function searchClub($clubID)
    {
        $club = Equestrian_club::where('id', $clubID)->first();

        if ($club) {
            $clubImages = ClubImage::where('club_id', $club->id)->get()->pluck('image_paths')->toArray();

            $data['clubImages'] = $clubImages;
            $response = [
                'message' => 'club was found successfully.',
                'club' => $club,
                'images' => $data['clubImages'],
                'status' => true
            ];

            return $response;
        } else {
            $response = [
                'message' => 'club does not exist.',
                'status' => false
            ];
            return $response;
        }

    }

    public function deleteClub($userId)
    {
        $club = User::where('id', $userId)->first();
        if ($club) {
            $club->delete();
            $response = [
                'message' => 'club was deleted successfully.',
                'status' => true
            ];

            return $response;
        } else {
            $response = [
                'message' => 'club does not exist.',
                'status' => false
            ];
            return $response;
        }

    }
}
