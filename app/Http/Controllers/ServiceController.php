<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CLUB\ClubServise;
use App\Models\CLUB\Equestrian_club;
use App\Models\CLUB\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;




use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function getClubServices($id){
         return " test";
    }

    public function index($id)
    {
        $club_id = Equestrian_club::where('user_id',$id)->first()->id;
        $services = Service::where('club_id', $club_id)->get();

        return response()->json([
            'services' => $services,
            'status'=> true
        ]);
    }
    public function allServicesForUser($id)
    {
        $services = Service::where('club_id', $id)->get();

        return response()->json([
            'services' => $services,
            'status'=> true
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        $user_id = Auth::id();

        $club_id = Equestrian_club::where('user_id',$user_id)->first()->id;


        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'image'=>'required',
            'category'=>'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }


        if($request->hasFile('image')){
            $file_extension = $request->image->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;

            $path = public_path('images/SERVICES/');
            $request->image->move($path, $filename);
            $realPath = 'images/SERVICES/'.$filename;
        }

        $service  = Service::create([
            'name' => $request->name,
            'description' =>$request->description,
            'image' => $realPath,
            'category_id'=>$request->category,
            'club_id'=>$club_id



        ]);

        return response()->json([
            'message' =>'Service is created successfully.',
            'service' => $service,
            'status' => true
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show($name)
    {
        $service = Service::where('name',$name)->get();

        if (!$service) {
            return response()->json([
                'message' => 'Service not found!',
                'status' => false
            ]);
        }

        return response()->json([
            'services' => $service,
            'status' => true
        ]);

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $service = Service::find($id);

       if($request->hasFile('image')){
           $file_extension = $request->image->getClientOriginalExtension();
           $filename = time() . '.' . $file_extension;
           $path = public_path('images/SERVICES/');
           $request->image->move($path, $filename);
           $realPath = 'images/SERVICES/'.$filename;
           $service->update(['image'=>$realPath]);
       }
        $attributes = array_filter($request->all(),function ($value){
            return !is_null($value);
        });

        $requestData = collect($attributes)->
        except(['image'])->toArray();

        $service->update($requestData);

        return response()->json([
            'message' =>'Service is updated successfully.',
            'service' => $service,
            'status' => true
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $service =Service::find($id);

        if (!$service) {
            return response()->json([
                'message' => 'Service not found!',
                'status' => false
            ]);
        }

        $service->delete();

        return response()->json([
            'message' =>'Service is deleted successfully.',
            'status' => true
        ]);
    }
}
