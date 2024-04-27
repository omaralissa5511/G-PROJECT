<?php

namespace App\Http\Controllers;

use App\Models\CLUB\Clas;
use App\Models\CLUB\Course;
use App\Models\CLUB\Equestrian_club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
{

    public function createClass(Request $request){

        $validate = Validator::make($request->all(), [

            'start' => 'required',
            'end' => 'required',
            'class' => 'required',
            'day' => 'required',
            'course_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }


        $clas = Clas::create([
            'class' => $request->class,
            'day' => $request->day,
            'start' => $request->start,
            'end' => $request->end,
            'course_id' => $request->course_id,
        ]);

        $response = [
            'message' => '$class is created successfully.',
            'course' => $clas,
            'status' => true
        ];

        return response()->json($response);

    }



    public function editClass($class_id,Request $request){

        $validate = Validator::make($request->all(), [

            'start' => 'required',
            'end' => 'required',
            'class' => 'required',
            'day' => 'required',
            'course_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }

        $clas = Clas::where('id',$class_id)->first();
        $clas -> update([
            'class' => $request->class,
            'day' => $request->day,
            'start' => $request->start,
            'end' => $request->end,
            'course_id' => $request->course_id,
        ]);
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
