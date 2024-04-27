<?php

namespace App\Http\Controllers;

use App\Events\NewUSERAdded;
use App\Http\Controllers\Controller;
use App\Models\CLUB\Category;
use App\Models\CLUB\ClubImage;
use App\Models\CLUB\Equestrian_club;
use App\Models\HealthCare;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{


    public function messages(Request $request){
        event(new NewUSERAdded($request->name,$request->message));
        return [];
    }


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
            'profile' => 'required',
            'website' => 'required',
            //'day' => 'required',
            //'start' => 'required',
            //'end' => 'required',
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

        $file_extension = $request->profile->getClientOriginalExtension();
        $filename3 = time() . '.' . $file_extension;
        $path = public_path('images/Equestrian_club/profile/');
        $request->profile->move($path, $filename3);
        $realPath1 = 'images/Equestrian_club/profile/' . $filename3;

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
            'license' => $realPath,
            'profile' =>$realPath1
        ]);


        ClubImage::create(['image_paths' => $imagePaths,
            'club_id' => $club->id]);

        $clubImages = ClubImage::where('club_id', $club->id)->get()->pluck('image_paths')->toArray();

        $data['user'] = $user;
        $data['club'] = $club;
        $data['clubImages'] = $clubImages;
        $user->assignRole('CLUB');
        $response = [
            'message' => 'User is created successfully.',
            'data' => $data,
            'status' => true
        ];
        return response()->json($response);
    }

    public function showClubs()
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

    public function searchClubByName($name)
    {

        $clubs = Equestrian_club::where('name', 'LIKE', $name . '%')->get();

        if ($clubs->isEmpty()) {
            $response = [
                'message' => 'No clubs found.',
                'status' => false
            ];
        } else {
            $clubImages = ClubImage::where('club_id', $clubs[0]->id)->pluck('image_paths')->toArray();

            $response = [
                'message' => 'Club(s) found successfully.',
                'clubs' => $clubs,
                'images' => $clubImages,
                'status' => true
            ];
        }

        return $response;
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


//////////////////// HEALTHCARE SECTION //////////////
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



//////////////////// CATEGORY SECTION ////////////////
    public function createCategory(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name',
            'description' => 'required',
            'image'=>'required'
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
        $path = public_path('images/CATEGORY');
        $request->image->move($path, $filename);
        $realPath = 'images/CATEGORY'.$filename;

        $category = Category::create([
            'name' => $request->name,
            'description' =>$request->description,
            'image' =>$realPath
        ]);

        return response()->json([
            'message' =>'Category is created successfully.',
            'category' => $category,
            'status' => true
        ]);
    }

    public function getCategories()
    {
        $categories = Category::all();

        return response()->json([
            'categories' => $categories,
            'status'=> true
        ]);
    }

    public function getCategory(Request $request)
    {
        $category = Category::find($request->id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found!',
                'status' => false
            ]);
        }

        return response()->json([
            'category' => $category,
            'status' => true
        ]);
    }

    public function updateCategory(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name',
            'description' => 'required',
            'image'=>'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }

        $category = Category::find($request->id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found!',
                'status' => false
            ]);
        }

        $file_extension = $request->image->getClientOriginalExtension();
        $filename = time() . '.' . $file_extension;
        $path = public_path('images/ADMIN/PROFILES');
        $request->image->move($path, $filename);
        $realPath = 'images/CATEGORY'.$filename;


        $category->update([
            'name' => $request->name,
            'description' =>$request->description,
            'image'=>$realPath
        ]);

        return response()->json([
            'message' =>'Category is updated successfully.',
            'category' => $category,
            'status' => true
        ]);
    }

    public function deleteCategory(Request $request)
    {
        $category = Category::find($request->id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found!',
                'status' => false
            ]);
        }

        $category->delete();

        return response()->json([
            'message' =>'Category is deleted successfully.',
            'status' => true
        ]);
    }

}

