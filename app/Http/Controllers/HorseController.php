<?php

namespace App\Http\Controllers;

use App\Models\CLUB\ClubImage;
use App\Models\Horse;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class HorseController extends Controller
{


    public function showHorse()
    {
        $horses = Horse::get();

        if ($horses) {
            $response = [
                'data' => $horses,
                'status' => true
            ];
            return $response;
        } else {
            $response = [
                'message' => 'there is no club',
                'status' => false
            ];
            return $response;
        }

    }


    public function MyHorses()
    {
        $user_id = Auth::id();
        $profile_id = Profile::where('id',$user_id)->first()->id;
        $horses = Horse::where('profile_id',$profile_id)->get();

        if ($horses) {
            $response = [
                'data' => $horses,
                'status' => true
            ];
            return $response;
        } else {
            $response = [
                'message' => 'there is no club',
                'status' => false
            ];
            return $response;
        }
    }

//
//    public function deleteHorse($userId)
//    {
//        $club = User::where('id', $userId)->first();
//        if ($club) {
//            $club->delete();
//            $response = [
//                'message' => 'club was deleted successfully.',
//                'status' => true
//            ];
//
//            return $response;
//        } else {
//            $response = [
//                'message' => 'club does not exist.',
//                'status' => false
//            ];
//            return $response;
//        }
//
//    }


}
