<?php

namespace App\Http\Controllers;

use App\Models\AppMessage;
use Illuminate\Http\Request;

class AppMessageController extends Controller
{
    public function updateMessage(Request $request)
    {
        $this->validate($request, [
            'message' => 'required'
        ]);

        AppMessage::updateOrCreate(
            ['id' => 1],
            ['message' => $request->message]
        );

        return response()->json(['message' => 'Message updated successfully', 'status' => 'true']);
    }

    public function getMessage()
    {
        $appMessage = AppMessage::find(1);

        if (!$appMessage) {
            return response()->json(['message' => 'Message not found', 'status' => 'false']);
        }

        return response()->json([
            'message' => $appMessage->message,
            'status' => 'true'
        ]);
    }
}
