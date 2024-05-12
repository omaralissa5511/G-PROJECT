<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    public function getProfile($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
                'status' => false
            ]);
        }

        $profile = $user->profiles;

        if (!$profile) {
            return response()->json([
                'message' => 'Profile not found for this user.',
                'status' => false
            ]);
        }

        return response()->json([
            'user' => $user,
            'status' => true
        ]);
    }
}
