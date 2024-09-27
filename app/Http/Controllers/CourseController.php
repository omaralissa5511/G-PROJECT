<?php

namespace App\Http\Controllers;
use App\Models\CLUB\Course;
use App\Models\CLUB\Equestrian_club;
use App\Models\CLUB\Service;
use App\Models\CLUB\Trainer;
use App\Models\User;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\CLUB\Reservation;

class CourseController extends Controller
{
    public function createCourse(Request $request){

        $validate = Validator::make($request->all(), [

            'description' => 'required|string|max:250',
            'begin' => 'required',
            'end' => 'required',
            'days' => 'required',
            'duration' => 'required',
            'trainer_id' => 'required',
            'service_id' => 'required',
        ]);

        //return $end = Carbon::parse($request->end);
        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }
        $begin = Carbon::parse($request->begin);
        $end = Carbon::parse($request->end);
        $days = json_encode($request->days);
        $user_id = Auth::id();
        $club_id = Equestrian_club::where('user_id',$user_id)->first()->id;
        $course = Course::create([
            'description' => $request->description,
            'begin' => $begin,
            'end' => $end,
            'days' => $days,
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
         $message = 'new course have been added .';
        Broadcast(new \App\Events\Course($message));

        return response()->json($response);

    }

    public function editCourse($CID,Request $request){

        $course = Course::where('id',$CID)->first();
     

        $attributes = array_filter($request->all(),function ($value){
            return !is_null($value);
        });
        if($attributes['days']){
            $attributes['days']= json_encode($request->days);
        }
          if($attributes['begin']){
                $attributes['begin']= json_encode($request->begin);
             
            }
            if($attributes['end']){
                $attributes['end']= json_encode($request->end);
            }
        $course->update($attributes);

        $course = Course::where('id',$CID)->first();
        $response = [
            'message' => 'course is update successfully.',
            'course' => $course,
            'status' => true
        ];

       $message = 'new course have been upadted .';
        Broadcast(new \App\Events\Course($message));
        return response()->json($response);

    }

    public function MyCourses (){

            $user_id = Auth::id();
             $club_id = Equestrian_club::where('user_id',$user_id)->first()->id;
            $courses = Course::where('club',$club_id)->get();
            foreach ($courses as $course){
                $course->days = json_decode($course->days) ;
                $course->days = explode(',', $course->days[0]);
            }

            if($courses){
                $response = [
                    'message' => 'courses found : ',
                    'courses' => $courses,
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

    public function MyCourses2 (){

        $user_id = Auth::id();
        $club_id = Equestrian_club::where('user_id',$user_id)->first()->id;
        $courses = Course::where('club',$club_id)->get();
        foreach ($courses as $course){
            $course->days = json_decode($course->days) ;
            $course->days = explode(',', $course->days[0]);
        }

        if($courses){
            foreach ($courses as $course){
                $trainerName = Trainer::where('id',$course->trainer_id)
                    ->first()->FName;
                $serviceName = Service::where('id',$course->service_id)
                    ->first()->name;
                $clubName = Equestrian_club::where('id',$course->club)
                    ->first()->name;
                $course->trainer_id = $trainerName;
                $course->service_id = $serviceName;
                $course->club = $clubName;
                if($course->valid == 1){
                    $course->valid = 'شغال';
                }
                if($course->valid == 0){
                    $course->valid = 'محجوز بالكامل';
                }
            }
             $response = [
                'message' => 'courses found : ',
                'courses' => $courses,
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
    
     public function CourseReservations($CID){

        return Reservation::where('course_id',$CID)->get();
    }

    public function Reserve_Details($rID){
        $data = [];
        $reserve =  Reservation::where('id',$rID)->first();
          $data['person_who_booked'] = Profile::where('user_id',$reserve->user_id)->first()->FName;
          $data['image'] = Profile::where('user_id',$reserve->user_id)->first()->profile;
            $data['res'] = $reserve;
          return $data;
    }

    public function getCoursesByUser (Request $request){

        $validate = Validator::make($request->all(), [

            'club_id' => 'required',
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


        $courses = Course::where('club',$request->club_id)
                           ->where('trainer_id',$request->trainer_id)
                           ->where('service_id',$request->service_id)->get();
        foreach ($courses as $course){
            $course->days = json_decode($course->days) ;
            $course->days = explode(',', $course->days[0]);
        }

        if($courses){
            $response = [
                'message' => 'courses found : ',
                'courses' => $courses,
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


        $courses = Course::where('description',$id)->get();

        foreach ($courses as $course){

            $course->days = json_decode($course->days) ;
            $course->days = explode(',', $course->days[0]);
            $trainerName = Trainer::where('id',$course->trainer_id)
                ->first()->FName;
            $serviceName = Service::where('id',$course->service_id)
                ->first()->name;
            $clubName = Equestrian_club::where('id',$course->club)
                ->first()->name;
            $course->trainer_id = $trainerName;
            $course->service_id = $serviceName;
            $course->club = $clubName;
            if($course->valid == 1){
                $course->valid = 'شغال';
            }
            if($course->valid == 0){
                $course->valid = 'محجوز بالكامل';
            }
        }


        if($courses){
            $response = [
                'message' => 'course: ',
                'courses' => $courses,
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
          $message = 'new course have been deleted .';
        Broadcast(new \App\Events\Course($message));
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

