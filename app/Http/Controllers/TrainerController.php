<?php

namespace App\Http\Controllers;

use App\Models\CLUB\ClubImage;
use App\Models\CLUB\Equestrian_club;
use App\Models\CLUB\Service;
use App\Models\CLUB\Trainer;
use App\Models\CLUB\TrainerTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TrainerController extends Controller
{

    public function editTrainer(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'FName' => 'required|string|max:250',
            'mobile' => 'required|max:250',
            'LName' => 'required|string|max:250',
            'license' => 'required',
            'image' => 'required',
            'qualifications' => 'required',
            'certifications' => 'required',
            'experience' => 'required',
            'specialties' => 'required',
            'address' => 'required'
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
        $path = public_path('images/Trainer/PROFILES/');
        $request->image->move($path, $filename);
        $realPath = 'images/Trainer/PROFILES/' . $filename;

        $file_extension = $request->license->getClientOriginalExtension();
        $filename1 = time() . '.' . $file_extension;
        $path = public_path('images/Trainer/license/');
        $request->license->move($path, $filename1);
        $realPath1 = 'images/Trainer/license/' . $filename1;

        $userID = Auth::id();
        $user = User::find($userID)->first();
        $user->update(['mobile' => $request->input('mobile'),]);

        $trainer = Trainer::where('user_id', $userID)->first();
        $trainer->update([

            'FName' => $request->FName,
            'LName' => $request->LName,
            'address' => $request->address,
            'qualifications' => $request->qualifications,
            'certifications' => $request->certifications,
            'experience' => $request->experience,
            'specialties' => $request->gender,
            'license' => $realPath1,
            'image' => $realPath
        ]);

        $trainer = Trainer::where('user_id', $userID)->first();
        $data['user'] = $user;
        $data['trainer'] = $trainer;

        $response = [
            'message' => 'profile is updated successfully.',
            'data' => $data,
            'status' => true
        ];

        return response()->json($response);
    }


    public function MyProfile()
    {

        $id = Auth::id();
        $trainer = Trainer::where('user_id', $id)->first();

        $response = [

            'trainer' => $trainer,
            'status' => true
        ];

        return $response;
    }


public function allTrainersInServiceCourse($service_id)
    {
        $service = Service::find($service_id);

        if (!$service) {
            return response()->json([
                'message' => 'Service not found',
                'status' => false
            ]);
        }

        $trainers = $service->trainers;

        return response()->json([
            'Trainers' => $trainers,
            'status' => true
        ]);
    }


    public function getTrainerByID($id)
    {

        $trainer = Trainer::where('id', $id)->first();

        $response = [
            'trainer' => $trainer,
            'status' => true
        ];

        return $response;
    }

    public function getTrainerTimes(Request $request)
    {
        $trainer_id = $request->trainer_id;
        $date = $request->date;

        $date_obj = Carbon::createFromFormat('Y-m-d', $date);
//        $day_name = $date_obj->format('l');
        if ($date_obj->isToday() || $date_obj->isFuture()) {
//        if ($day_name === 'Friday' || $day_name === 'Saturday') {
//            return response()->json([
//                'message' => 'It\'s the weekend! No available times.',
//                'status' => false
//            ]);
//        }
//
//        // ابحث عن السجل الموجود لهذا اليوم
//        $existingRecord = TrainerTime::where('trainer_id', $trainer_id)
//            ->where('date', $date)
//            ->first();
//
//        if (!$existingRecord) {
//            // إذا لم يكن موجود أنشئ سجلات لكل ساعة في اليوم
//            $availableTimes = [];
//            $currentHour = 9;
//
//            while ($currentHour < 17) {
//                $start_time = sprintf('%02d:00', $currentHour);
//                $end_time = sprintf('%02d:00', $currentHour + 1);
//
//                $newRecord = TrainerTime::create([
//                    'trainer_id' => $trainer_id,
//                    'date' => $date,
//                    'start_time' => $start_time,
//                    'end_time' => $end_time,
//                    'is_available' => true,
//                ]);
//
//                $availableTimes[] = $newRecord;
//
//                $currentHour++;
//            }
//
//            return response()->json([
//                'Available Times' => $availableTimes,
//                'status' => true
//            ]);
//
//        }
//
        // إذا كان اليوم موجود يعيد الأوقات المتاحة
        $availableTimes = TrainerTime::where('trainer_id', $trainer_id)
            ->where('date', $date)
            ->where('is_available', true)
            ->get();

        return response()->json([
            'Available Times' => $availableTimes,
            'status' => true
        ]);
        } else {
            return response()->json([
                'message' => 'The given date is before today.',
                'status' => false
            ]);
        }
    }


    public function reserveTrainerTimes($trainerTime_ids)
    {
        foreach ($trainerTime_ids as $trainerTime_id) {


        TrainerTime::where([
            'id' => $trainerTime_id
        ])->update(['is_available' => false]);
       }

        return response()->json([
            'message' => 'Times have been booked successfully!',
            'status'=>true
            ]);
    }

    public function setAvailableTimes(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'trainer_id' => 'required',
            'date' => 'required',
            'available_times' => 'required|array',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }

        $trainerId = $request->trainer_id;
        $date = $request->date;
        $availableTimes = $request->available_times;

        // حفظ الأوقات المتاحة في جدول وقت المدرب
        foreach ($availableTimes as $time) {
            $startTime = $time['start_time'];
            $endTime = $time['end_time'];
            $price = $time['price']; // إضافة السعر

            $existingTime = TrainerTime::where('trainer_id', $trainerId)
                ->where('date', $date)
                ->where('start_time', $startTime)
                ->where('end_time', $endTime)
                ->first();

            if ($existingTime) {
                return response()->json([
                    'message' => 'This time slot is already available.',
                    'date' => $date,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => false
                ]);
            }

            TrainerTime::create([
                'trainer_id' => $trainerId,
                'date' => $date,
                'start_time' => $time['start_time'],
                'end_time' => $time['end_time'],
                'price' => $price, // إضافة السعر
                'is_available' => true,
            ]);
        }

        return response()->json([
            'message' => 'The times have been filled successfully.',
            'status' => true
        ]);
    }


}
