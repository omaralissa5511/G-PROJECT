<?php

namespace App\Http\Controllers;

use App\Models\CLUB\Clas;
use App\Models\CLUB\Course;
use App\Models\CLUB\Equestrian_club;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
{

    public function createClass(Request $request){

        $validate = Validator::make($request->all(), [

            'start' => 'required',
            'end' => 'required',
            'price' => 'required',
            'class' => 'required',
            'capacity' => 'required',
            'course_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }

        $begin = Carbon::parse($request->begin);
        $end = Carbon::parse($request->end);


        $clas = Clas::create([
            'class' => $request->class,
            'capacity' => $request->capacity,
            'price' => $request->price,
            'start' => $begin,
            'end' => $end,
            'course_id' => $request->course_id,
            'counter' => 0,
            'status' => 0,
        ]);

        $response = [
            'message' => '$class is created successfully.',
            'course' => $clas,
            'status' => true
        ];

        return response()->json($response);

    }



    public function editClass($class_id,Request $request){

        $clas = Clas::where('id',$class_id)->first();
        $attributes = array_filter($request->all(),function ($value){
            return !is_null($value);
        });
        $clas -> update($attributes);
        $clas = Clas::where('id',$class_id)->first();
        $response = [
            'message' => '$class is updated successfully.',
            'course' => $clas,
            'status' => true
        ];

        return response()->json($response);

    }


    public function getCourseClasses ($course_id){


        $classes = Clas::where('course_id',$course_id)->get();
        if($classes){
              forEach($classes as $cla){
                  if($cla->status == 0){
                      $cla->status = 'شغال';
                  }else{
                      $cla->status = 'مليان';
                  }
              }
            $response = [
                'message' => 'classes found : ',
                'classes' => $classes,
                'status' => true
            ];
            return $response;
        } else{
            $response = [
                'message' => 'no classes for you.',
                'status' => false
            ];
            return $response;
        }

    }


    public function deleteClass ($id){

        $clas = Clas::where('id',$id)->first();
        if($clas) {
            $clas->delete();
            $response = [
                'message' => 'the class was removed successfully.',
                'status' => true
            ];

            return $response;}
        else {
            $response = [
                'message' => 'class does not exist.',
                'status' => false
            ];
            return $response;
        }

    }

}
