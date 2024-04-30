<?php

namespace App\Http\Controllers;

use App\Events\TrainerCHAT;
use App\Models\CLUB\Trainer;
use App\Models\MessageM;
use Illuminate\Http\Request;


class MessageController extends Controller
{

    public function sendMessage(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
            'trainer_id' => 'required',
            'content' => 'required',
        ]);
        $message = new MessageM();
        if($request->file('images')){
            $images = $request->file('images');
            $imagePaths = [];
            foreach ($images as $image) {
                $new_name = rand() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/CHAT/'), $new_name);
                $imagePaths[] = 'images/CHAT/'. $new_name;
            }
            $message->image = $imagePaths;
        }

        $message->user_id = $validatedData['user_id'];
        $message->trainer_id = $validatedData['trainer_id'];
        $message->content = $validatedData['content'];
        $message->save();

        Trainer::where('id',$request->trainer_id)
            ->first()->channelName;
        broadcast(new TrainerCHAT($message))->toOthers();

        return response()->json(['success' => true,
            'message' => 'MessageM sent successfully.',
            $message]);
    }
}




