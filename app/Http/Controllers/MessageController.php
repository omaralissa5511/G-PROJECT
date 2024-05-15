<?php

namespace App\Http\Controllers;

use App\Events\CHAT;
use App\Events\TrainerCHAT;
use App\Models\CLUB\Trainer;
use App\Models\MessageM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;


class MessageController extends Controller
{

    public function sendMessage(Request $request)
    {


        $validatedData = $request->validate([
            'user_id' => 'required',
            'trainer_id' => 'required',
            'content' => 'required',
        ]);

//        if($request->file('images')){
//            $images = $request->file('images');
//            $imagePaths = [];
//            foreach ($images as $image) {
//                $new_name = rand() . '.' . $image->getClientOriginalExtension();
//                $image->move(public_path('images/CHAT/'), $new_name);
//                $imagePaths[] = 'images/CHAT/'. $new_name;
//            }
//            $message->image = $imagePaths;
//        }
//
          $message = new MessageM();

         if($request->file('image')) {
             $file_extension = $request->image->getClientOriginalExtension();
             $filename = time() . '.' . $file_extension;
             $path = public_path('images/CHAT/');
             $request->image->move($path, $filename);
             $realPath = 'images/CHAT/' . $filename;
             $message->image = $realPath;

         }
        $message->user_id = $validatedData['user_id'];
        $message->trainer_id = $validatedData['trainer_id'];
        $message->content = $validatedData['content'];
        $message->role = $request->role;
        $message->user = $request->user;
        $message->trainer = $request->trainer;
        $message->save();


        broadcast(new CHAT( $message->user_id,$message->trainer_id,$message));

        return response()->json([
            'success' => true,
            'message' => 'MessageM sent successfully.',
            $message]);
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
        $channelName = $request->input('channelName');

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
}



