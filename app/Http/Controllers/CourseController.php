<?php

namespace App\Http\Controllers;
use App\Models\CLUB\Course;
use App\Models\CLUB\Equestrian_club;
use App\Models\CLUB\Trainer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function createCourse(Request $request){

        $validate = Validator::make($request->all(), [

            'description' => 'required|string|max:250',
            'price' => 'required',
            'begin' => 'required',
            'end' => 'required',
            'duration' => 'required',
            'trainer_id' => 'required',
            'service_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }
        $user_id = Auth::id();
        $club_id = Equestrian_club::where('user_id',$user_id)->first()->id;
        $course = Course::create([
            'description' => $request->description,
            'price' => $request->price,
            'begin' => $request->begin,
            'end' => $request->end,
            'valid' => true,
            'club'=>$club_id,
            'duration' => $request->duration,
            'trainer_id' => $request->trainer_id,
            'service_id' => $request->service_id,
        ]);

        $response = [
            'message' => 'course is created successfully.',
            'course' => $course,
            'status' => true
        ];

        return response()->json($response);

    }

    public function editCourse($CID,Request $request){

        $validate = Validator::make($request->all(), [

            'description' => 'required|string|max:250',
            'price' => 'required',
            'begin' => 'required',
            'end' => 'required',
            'duration' => 'required',
            'trainer_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }

        $course = Course::where('id',$CID)->first();
        $course -> update([
            'description' => $request->description,
            'price' => $request->price,
            'begin' => $request->begin,
            'end' => $request->end,
            'duration' => $request->duration,
            'trainer_id' => $request->trainer_id,
        ]);
        $course = Course::where('id',$CID)->first();
        $response = [
            'message' => 'course is update successfully.',
            'course' => $course,
            'status' => true
        ];

        return response()->json($response);

    }

    public function MyCourses (){

            $user_id = Auth::id();
            $club_id = Equestrian_club::where('user_id',$user_id)->first()->id;
            $courses = Course::where('club',$club_id)->get();
            if($courses){
                $response = [
                    'message' => 'courses found : ',
                    'trainers' => $courses,
                    'status' => true
                ];
                return $response;
            }else{
                $response = [
                    'message' => 'no courses for you.',
                    'status' => false
                ];
                return $response;
            }

        }

    public function getSpecificCourse ($id){


        $course = Course::where('id',$id)->first();

        if($course){
            $response = [
                'message' => 'course: ',
                'trainers' => $course,
                'status' => true
            ];
            return $response;
        }else{
            $response = [
                'message' => 'no course for you.',
                'status' => false
            ];
            return $response;
        }

    }

    public function allCoursesInTrainer($trainer_id)
    {

        $courses = Course::where('trainer_id', $trainer_id)->get();

        return response()->json([
            'courses' => $courses,
            'status'=> true
        ]);
    }



    public function deleteCourse ($id){

        $course = Course::where('id',$id)->first();
        if($course) {
            $course->delete();
            $response = [
                'message' => 'the course was removed successfully.',
                'status' => true
            ];

            return $response;}
        else {
            $response = [
                'message' => 'course does not exist.',
                'status' => false
            ];
            return $response;
        }

    }


}
