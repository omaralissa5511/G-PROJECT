<?php

namespace App\Http\Controllers\CLUB;

use App\Http\Controllers\Controller;
use App\Models\CLUB\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
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

        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'image'=>'required',
            'category_id'=>'required',
            'club_id'=>'required'
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
        $path = public_path('images/SERVICES');
        $request->image->move($path, $filename);
        $realPath = 'images/SERVICES'.$filename;

        $service = Service::create([
            'name' => $request->name,
            'description' =>$request->description,
            'image' =>$realPath,
            'category_id'=>$request->category_id,
            'club_id'=>$request->club_id
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
    public function show($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'message' => 'Service not found!',
                'status' => false
            ]);
        }

        return response()->json([
            'category' => $service,
            'status' => true
        ]);

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
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

        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'message' => 'Service not found!',
                'status' => false
            ]);
        }

        $file_extension = $request->image->getClientOriginalExtension();
        $filename = time() . '.' . $file_extension;
        $path = public_path('images/SERVICES');
        $request->image->move($path, $filename);
        $realPath = 'images/SERVICES'.$filename;



        $service->update([
            'name' => $request->name,
            'description' =>$request->description,
            'image'=>$realPath
        ]);

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
