<?php

namespace App\Http\Controllers;

use App\Models\CLUB\Service;
use App\Models\CLUB\Trainer;
use Illuminate\Http\Request;

class TrainerServiceController extends Controller
{

    public function addTrainerToService(Request $request)
    {
        try {

            $service = Service::find($request->service_id);
            $trainer = Trainer::find($request->trainer_id);

            if(!$service || !$trainer )
                return response()->json([
                    'message' => 'Service or trainer not found.',
                    'status' => false
                ]);

            $service->b_trainers()->attach($trainer);

            return response()->json([
                'message' => "The trainer has been successfully added to the service",
                'status'=>'true'
            ]);
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function removeTrainerFromService(Request $request)
    {
        try {

            $service = Service::find($request->service_id);
            $trainer = Trainer::find($request->trainer_id);

            if (!$service || !$trainer) {
                return response()->json([
                    'message' => 'Service or trainer not found.',
                    'status' => false
                ]);
            }


            $service->b_trainers()->detach($trainer);


            return response()->json([
                'message' => "The trainer has been successfully removed from the service.",
                'status' => true
            ]);
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function allTrainersInServiceBooking($service_id)
    {
        $service = Service::find($service_id);

        if (!$service) {
            return response()->json([
                'message' => 'Service not found',
                'status' => false
            ]);
        }

        $trainers = $service->b_trainers;

        return response()->json([
            'Trainers' => $trainers,
            'status' => true
        ]);
    }



}
