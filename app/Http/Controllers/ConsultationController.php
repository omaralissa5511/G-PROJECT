<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\ConsultationDetails;
use App\Models\ConsultationImage;
use App\Models\HealthCare;
use App\Models\Profile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsultationController extends Controller
{
    public function createConsultation(Request $request){

        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'age' => 'required',
            'gender' => 'required',
            'color' => 'required',
            'symptoms' => 'required',
            'question' => 'required',
            'profile_id'=>'required',
            'health_care_id'=>'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }



        $currentTime = Carbon::now()->format('Y-m-d H:i');
        $consultation=Consultation::create([
            'name'=>$request->name,
            'age'=>$request->age,
            'gender'=>$request->gender,
            'color'=>$request->color,
            'symptoms'=>$request->symptoms,
            'content'=>$request->question,
            'sent_at'=>$currentTime,
            'profile_id'=>$request->profile_id,
            'health_care_id'=>$request->health_care_id,
        ]);

        if($request->hasFile('images')){
            $images = $request->file('images');

            foreach ($images as $image) {
                $file_extension = $image->getClientOriginalExtension();
                $filename = time() . '.' . $file_extension;
                $path = public_path('images/Consultation/');
                $image->move($path, $filename);
                $realPath = 'images/Consultation/'.$filename;
                ConsultationImage::create(['image' => $realPath,
                    'consultation_id' => $consultation->id]);
            }
        }

        if($request->has('vaccinations')) {

            $vaccinations = $request->vaccinations;

            foreach ($vaccinations as $vaccination) {
                ConsultationDetails::create([
                    'consultation_id' => $consultation->id,
                    'details' => $vaccination['details'],
                    'date' => $vaccination['date'],
                    'type' => 'vaccination'
                ]);
            }
        }

        if($request->has('treatments')) {
 //           $treatments = json_decode($request->treatments, true);
            $treatments =$request->treatments;
            foreach ($treatments as $treatment) {
                ConsultationDetails::create([
                    'consultation_id'=>$consultation->id,
                    'details'=>$treatment['details'],
                    'date'=>$treatment['date'],
                    'type'=>'treatment'

                ]);
            }
        }
        if($request->has('medicals')) {
//            $medicals = json_decode($request->medicals, true);
            $medicals = $request->medicals;
            foreach ($medicals as $medical) {
                ConsultationDetails::create([
                    'consultation_id' => $consultation->id,
                    'details' => $medical['details'],
                    'date' => $medical['date'],
                    'type' => 'medical'

                ]);
            }
        }

        $consultation->details=$consultation->consultation_details->groupBy('type');;
        $consultation->images=$consultation->consultation_images->pluck('image');
        unset($consultation->consultation_details);
        unset($consultation->consultation_images);

        //notification
        $profile_name=Profile::where('id',$consultation->profile_id)->first()->name;
        $healthCare=HealthCare::where('id',$consultation->health_care_id)->first()->user_id;
        $user1 = User::where('id',$healthCare)->first();
        $notificationService = new \App\Services\Api\NotificationService();
        $notificationService->send($user1, 'A new consultation', 'There is a new consultation by '.$profile_name);
        // end notification
        return response()->json([
           "message"=>"Consultation is added successfully",
            "Consultation"=>$consultation,
            "status"=>true
        ]);
    }

    public function replyConsultation($id,Request $request){
        $validate = Validator::make($request->all(), [
            'reply_content'=>'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }

        $consultation=Consultation::where('id',$id)->first();
        if(!$consultation)
            return response()->json([
                'message' => 'Consultation not found.',
                'status' => false
            ]);

        $currentTime = Carbon::now()->format('Y-m-d H:i');
        $consultation->update([
           'reply_content'=>$request->reply_content,
            'reply_sent_at'=>$currentTime
        ]);

        $consultation->details=$consultation->consultation_details->groupBy('type');;
        $consultation->images=$consultation->consultation_images->pluck('image');
        unset($consultation->consultation_details);
        unset($consultation->consultation_images);

        //notification
        $user=Profile::where('id',$consultation->profile_id)->first()->user_id;
        $healthCare=HealthCare::where('id',$consultation->health_care_id)->first()->name;
        $user1 = User::where('id',$user)->first();
        $notificationService = new \App\Services\Api\NotificationService();
        $notificationService->send($user1, 'Your consultation has been responded to.', 'Your consultation has been responded to.');
        // end notification

        return response()->json([
            "message"=>"The consultation was answered successfully",
            "Consultation"=>$consultation,
            "status"=>true
        ]);
    }

    public function allConsultationByUser($id){
        $cons=Consultation::where('profile_id',$id)->first();
        if($cons){
        $consultations=Consultation::where('profile_id',$id)->get();

        foreach ($consultations as $consultation) {
            $consultation->details = $consultation->consultation_details->groupBy('type');
            $consultation->images = $consultation->consultation_images->pluck('image');
            $consultation->sent_at=Carbon::parse($consultation->sent_at)->format('Y-m-d H:i');
            if($consultation->reply_sent_at)
                $consultation->reply_sent_at=Carbon::parse($consultation->reply_sent_at)->format('Y-m-d H:i');
            unset($consultation->consultation_details);
            unset($consultation->consultation_images);
        }

        return response()->json([
            "Consultation"=>$consultations,
            "status"=>true
        ]);
        }else
            return response()->json([
            'message' => 'The user does not have any Consultation',
            'status' => false
            ]);
    }

    public function allConsultationByHealthCare($id)
    {
        $cons=Consultation::where('health_care_id',$id)->first();
        if($cons){
        $consultations = Consultation::where('health_care_id', $id)->get();
            foreach ($consultations as $consultation) {
                $consultation->sent_at=Carbon::parse($consultation->sent_at)->format('Y-m-d H:i');
                if($consultation->reply_sent_at)
                    $consultation->reply_sent_at=Carbon::parse($consultation->reply_sent_at)->format('Y-m-d H:i');

                $consultation->details = $consultation->consultation_details->groupBy('type');
                $consultation->images = $consultation->consultation_images->pluck('image');
                unset($consultation->consultation_details);
                unset($consultation->consultation_images);
            }

            return response()->json([
                "Consultation" => $consultations,
                "status" => true
            ]);
        }else
            return response()->json([
                'message' => 'The Health Care does not have any Consultation',
                'status' => false
            ]);
    }

    public function allUnansweredConsultationsByHealthCare($id)
    {
        $cons=Consultation::where('health_care_id',$id)->first();
        if($cons){
            $consultations = Consultation::where('health_care_id', $id)->where('reply_content',null)->get();
            foreach ($consultations as $consultation) {
                $consultation->sent_at=Carbon::parse($consultation->sent_at)->format('Y-m-d H:i');
                $consultation->details = $consultation->consultation_details->groupBy('type');
                $consultation->images = $consultation->consultation_images->pluck('image');
                unset($consultation->consultation_details);
                unset($consultation->consultation_images);
            }

            return response()->json([
                "Consultation" => $consultations,
                "status" => true
            ]);
        }else
            return response()->json([
                'message' => 'The Health Care does not have any Consultation',
                'status' => false
            ]);
    }

    public function getConsultationByID($id){
        $consultation=Consultation::where('id',$id)
            ->with(['profile' => function ($query) {
            $query->addSelect(['email' => User::select('email')  //لاضافة email لمعلومات ال profile
            ->whereColumn('id', 'profiles.user_id')
            ]);
        },'health_care'])->first();

        if($consultation){
            $consultation->sent_at=Carbon::parse($consultation->sent_at)->format('Y-m-d H:i');
            if($consultation->reply_sent_at)
                $consultation->reply_sent_at=Carbon::parse($consultation->reply_sent_at)->format('Y-m-d H:i');

            $consultation->details = $consultation->consultation_details->groupBy('type');
            $consultation->images = $consultation->consultation_images->pluck('image');
            unset($consultation->consultation_details);
            unset($consultation->consultation_images);

            $consultation->health_care->day=json_decode($consultation->health_care->day);
            $consultation->health_care->day = explode(',', $consultation->health_care->day[0]);
            $consultation->health_care->start=Carbon::parse($consultation->health_care->start)->format('H:i');
            $consultation->health_care->end=Carbon::parse($consultation->health_care->end)->format('H:i');

            return response()->json([
                "Consultation" => $consultation,
                "status" => true
            ]);
        }else
            return response()->json([
                'message' => 'Consultation not found.',
                'status' => false
            ]);

    }

    public function deleteConsultation($id){

        $consultation=Consultation::where('id',$id)->first();
        if($consultation) {
            $consultation->delete();
            return response()->json([
                'message' => 'Consultation was removed successfully.',
                'status' => true
            ]);
        }
        else
            return response()->json([
                'message' => 'Consultation does not exist.',
                'status' => false
            ]);

    }
}
