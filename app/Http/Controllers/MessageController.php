<?php

namespace App\Http\Controllers;

use App\Events\CHAT;
use App\Events\DOCTOR_CHAT;
use App\Events\TrainerCHAT;
use App\Events\Users;
use App\Models\CLUB\Trainer;
use App\Models\Doctor;
use App\Models\MessageD;
use App\Models\MessageM;
use App\Models\Profile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;


class MessageController extends Controller
{
    
    public function authenticate_V2(Request $request)
    {

//        $pusher = new Pusher(
//            env('PUSHER_APP_KEY'),
//            env('PUSHER_APP_SECRET'),
//            env('PUSHER_APP_ID'),
//            [
//                'cluster' => env('PUSHER_APP_CLUSTER'),
//                'useTLS' => true,
//            ]
//        );

//        $socketId = $request->input('socketId');
//        $channelName = $request->input('channel_name');

//        $auth = $pusher->socket_auth($channelName, $socketId);
//        $auth = \GuzzleHttp\json_decode($auth);
       // return response()->json($pusher->socket_auth($channelName, $socketId));
    }



    public function sendMessage(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
            'trainer_id' => 'required',
        ]);

          $message = new MessageM();

         if($request->file('image')) {
             $file_extension = $request->image->getClientOriginalExtension();
             $filename = time() . '.' . $file_extension;
             $path = public_path('images/CHAT/');
             $request->image->move($path, $filename);
             $realPath = 'images/CHAT/' . $filename;
             $message->image = $realPath;

         }
        $currentTime = Carbon::now();
        $formattedTime = $currentTime->format('h:i A');
        $message->user_id = $validatedData['user_id'];
        $message->trainer_id = $validatedData['trainer_id'];
        $message->content = $request->cont;
        $message->role = $request->role;
        $message->user = $request->user;
        $message->trainer = $request->trainer;
        $message->time = $formattedTime;
        $message->save();

        $name = Profile::where('user_id',$request->user_id)->first()->FName;
        $message->user_id = $name;
        $mm = MessageM::query()->latest()->first();


        broadcast(new CHAT( $message->user_id,$message->trainer_id,$mm));

        return response()->json([
            'success' => true,
            'message' => 'MessageM sent successfully.',
            $mm]);
    }

    public function authenticate(Request $request)
    {


        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ]
        );

        $socketId = $request->input('socketId');
        $channelName = $request->input('channel_name');

//        $auth = $pusher->socket_auth($channelName, $socketId);
//        $auth = \GuzzleHttp\json_decode($auth);
     return response()->json($pusher->socket_auth($channelName, $socketId));
    }


    public function getChatMessages(Request $request)
    {

        $userID = $request->user_id;
        $trainerID = $request->trainer_id;
        $chat = MessageM::where('user_id', $userID)
            ->where('trainer_id', $trainerID)->get();
        if ($chat) {
            return response()->json([
                'success' => true,
                'message' => 'Messages sent successfully.',
                'chats' => $chat
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'No Messages',
            ]);
        }
    }


    public function send_Doctor_Message(Request $request)
    {


        $validatedData = $request->validate([
            'user_id' => 'required',
            'doctor_id' => 'required',
        ]);

        $message = new MessageD();

        if($request->file('image')) {
            $file_extension = $request->image->getClientOriginalExtension();
            $filename = time() . '.' . $file_extension;
            $path = public_path('images/CHAT/');
            $request->image->move($path, $filename);
            $realPath = 'images/CHAT/' . $filename;
            $message->image = $realPath;

        }
        $currentTime = Carbon::now();
        $formattedTime = $currentTime->format('h:i A');
        $message->user_id = $validatedData['user_id'];
        $message->doctor_id = $validatedData['doctor_id'];
        $message->content = $request->cont;
        $message->role = $request->role;
        $message->user = $request->user;
        $message->doctor = $request->doctor;
        $message->time = $formattedTime;
        $message->save();


        broadcast(new DOCTOR_CHAT( $message->user_id,$message->doctor_id,$message));

        return response()->json([
            'success' => true,
            'message' => 'MessageD sent successfully.',
            $message]);
    }


    public function getDoctor_ChatMessages(Request $request)
    {

        $userID = $request->user_id;
        $doctorID = $request->doctor_id;
        $chat = MessageD::where('user_id', $userID)
            ->where('doctor_id', $doctorID)->get();
        if ($chat) {
            return response()->json([
                'success' => true,
                'message' => 'Messages sent successfully.',
                'chats' => $chat
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'No Messages',
            ]);
        }
    }


    public function getAllUser(){
        $user = User::where('type','profile')->with('profiles')-> get();
        return $user;
    }

    public function allTrainerChatsByUser($id)
    {
        $trainers = MessageM::where('user_id', $id)->get('trainer_id');
        $chatList = [];
        foreach ($trainers as $trainer) {
            $trainerInfo = Trainer::where('id', $trainer->trainer_id)->first();
            $lastMessage = MessageM::where('user_id', $id)
                ->where('trainer_id', $trainer->trainer_id)
                ->orderBy('created_at', 'desc')
                ->first();
            $read = MessageM::where('user_id', $id)->where('trainer_id', $trainer->trainer_id)
                ->where('user', 0)->where('read', 0)->count();
            $ids = MessageM::where('user_id', $id)->where('trainer_id', $trainer->trainer_id)
                ->where('user', 0)->where('read', 0)->pluck('id');
            if ($trainerInfo) {
                if ($lastMessage) {
                    $chatList[$trainer->trainer_id] = [
                        'trainer_id' => $trainer->trainer_id,
                        'trainer_name' => $trainerInfo->FName . ' ' . $trainerInfo->lName,
                        'trainer_image' => $trainerInfo->image,
                           'sort' => $lastMessage->created_at,
                        'last_message' => $lastMessage->content,
                        'last_message_time' => $lastMessage->time,
                        'unread_messages' => $read,
                        'ids'=>$ids
                    ];
                }
            }else
                continue;
        }
        // ترتيب القائمة بناءً على الوقت
        usort($chatList, function ($a, $b) {
            return strtotime($b['sort']) - strtotime($a['sort']);
        });

        if (!empty($chatList)) {
            return response()->json([
                'chatList' => array_values($chatList), // إعادة ترتيب المفاتيح
                'status' => true,
            ]);
        } else {
            return response()->json([
                'message' => 'No messages found for this user.',
                'status' => false,
            ]);
        }
    }

    public function allDoctorChatsByUser($id)
    {
        $doctors = MessageD::where('user_id', $id)->get('doctor_id');
        $chatList = [];
        foreach ($doctors as $doctor) {
            $doctorInfo = Doctor::where('id', $doctor->doctor_id)->first();
            $lastMessage = MessageD::where('user_id', $id)
                ->where('doctor_id', $doctor->doctor_id)
                ->orderBy('created_at', 'desc')
                ->first();
            $read=MessageD::where('user_id', $id)->where('doctor_id', $doctor->doctor_id)
                ->where('user',0)->where('read',0)->count();
            $ids=MessageD::where('user_id', $id)->where('doctor_id', $doctor->doctor_id)
                ->where('user',0)->where('read',0)->pluck('id');
            if ($doctorInfo) {
                if ($lastMessage) {
                    $chatList[$doctor->doctor_id] = [
                        'doctor_id' => $doctor->doctor_id,
                        'doctor_name' => $doctorInfo->firstName . ' ' . $doctorInfo->lastName,
                        'doctor_image' => $doctorInfo->image,
                        'sort' => $lastMessage->created_at,
                        'last_message' => $lastMessage->content,
                        'last_message_time' => $lastMessage->time,
                        'unread_messages' => $read,
                        'ids'=>$ids
                    ];
                }
            }else
                continue;
        }

        // ترتيب القائمة بناءً على الوقت
        usort($chatList, function ($a, $b) {
            return strtotime($b['sort']) - strtotime($a['sort']);
        });

        if (!empty($chatList)) {
            return response()->json([
                'chatList' => array_values($chatList), // إعادة ترتيب المفاتيح
                'status' => true,
            ]);
        } else {
            return response()->json([
                'message' => 'No messages found for this user.',
                'status' => false,
            ]);
        }
    }

    public function isReadTrainer($message_id){
        $read=MessageM::where('id',$message_id)->update(['read'=>1]);
        return response()->json([
            'message' => 'Message is read',
            'status' => true,
        ]);
    }
    
    public function isReadDoctor($message_id){
        $read=MessageD::where('id',$message_id)->update(['read'=>1]);
        return response()->json([
            'message' => 'Message is read',
            'status' => true,
        ]);
    }
    
      
    public function getAllUsersThat_A_Doctor_chatsWith($id)
    {
         $users = MessageD::where('doctor_id', $id)->get('user_id');
        $chatList = [];
        foreach ($users as $user) {
            $userInfo = Profile::where('user_id', $user->user_id)->first();
            $lastMessage = MessageD::where('doctor_id', $id)
                ->where('user_id', $user->user_id)
                ->orderBy('created_at', 'desc')
                ->first();
            $read = MessageD::where('doctor_id', $id)->where('user_id', $user->user_id)
                ->where('doctor', 0)->where('read', 0)->count();
            $ids = MessageD::where('user_id', $user->user_id)->
                where('doctor_id', $id)
                ->where('doctor', 0)->where('read', 0)->pluck('id');
            if ($userInfo) {
                if ($lastMessage) {
                    $chatList[$user->user_id] = [
                        'user_id' => $user->user_id,
                        'user_name' => $userInfo->FName . ' ' . $userInfo->lName,
                        'last_message' => $lastMessage->content,
                        'last_message_time' => $lastMessage->time,
                        'image' => $userInfo->profile,
                         'sort' => $lastMessage->created_at,
                        'unread_messages' => $read,
                        'ids'=>$ids
                    ];
                }
            }else
                continue;
        }
        // ترتيب القائمة بناءً على الوقت
        usort($chatList, function ($a, $b) {
            return strtotime($b['sort']) - strtotime($a['sort']);
        });

        if (!empty($chatList)) {
            return response()->json([
                'chatList' => array_values($chatList), // إعادة ترتيب المفاتيح
                'status' => true,
            ]);
        } else {
            return response()->json([
                'message' => 'No messages found for this user.',
                'status' => false,
            ]);
        }
    }
    
    
      public function getAllUsersThat_A_Trainer_chatsWith($id)
    {
        $users = MessageM::where('trainer_id', $id)->get('user_id');
        $chatList = [];
        foreach ($users as $user) {
            $userInfo = Profile::where('user_id', $user->user_id)->first();
            $lastMessage = MessageM::where('trainer_id', $id)
                ->where('user_id', $user->user_id)
                ->orderBy('created_at', 'desc')
                ->first();
            $read = MessageM::where('trainer_id', $id)->where('user_id', $user->user_id)
                ->where('trainer', 0)->where('read', 0)->count();
            $ids = MessageM::where('user_id', $user->user_id)->
            where('trainer_id', $id)
                ->where('trainer', 0)->where('read', 0)->pluck('id');
            if ($userInfo) {
                if ($lastMessage) {
                    $chatList[$user->user_id] = [
                        'user_id' => $user->user_id,
                        'user_name' => $userInfo->FName . ' ' . $userInfo->lName,
                        'last_message' => $lastMessage->content,
                        'last_message_time' => $lastMessage->time,
                        'image' => $userInfo->profile,
                        'sort' => $lastMessage->created_at,
                        'unread_messages' => $read,
                        'ids'=>$ids
                    ];
                }
            }else
                continue;
        }
        // ترتيب القائمة بناءً على الوقت
        usort($chatList, function ($a, $b) {
            return strtotime($b['sort']) - strtotime($a['sort']);
        });

        if (!empty($chatList)) {
            return response()->json([
                'chatList' => $chatList, // إعادة ترتيب المفاتيح
                'status' => true,
            ]);
        } else {
            return response()->json([
                'message' => 'No messages found for this user.',
                'status' => false,
            ]);
        }
    }
}



