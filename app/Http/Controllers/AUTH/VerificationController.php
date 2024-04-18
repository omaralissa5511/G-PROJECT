<?php

namespace App\Http\Controllers\AUTH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\VerificationEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{

    public function sendVerificationEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $verificationCode = rand(100000, 999999); // Generate a random six digit code
            $user->verificationCode = $verificationCode;
            $user->verification_code_expires_at = now()->addMinutes(15);
            $user->save();

            Mail::to($user->email)->send(new VerificationEmail($user));

            return response()->json(['message' => 'Verification email sent.']);
        } else {
            return response()->json(['message' => 'User not found.'], 404);
        }
    }

    public function verify(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            if ($user->verificationCode == $request->verificationCode) {
                if ($user->verification_code_expires_at > now()) {
                    $user->email_verified_at = now();
                    $user->save();

                    return response()->json(['message' => 'Email verified successfully.']);
                } else {
                    return response()->json(['message' => 'Verification code expired.'], 400);
                }
            } else {
                return response()->json(['message' => 'Invalid verification code.'], 400);
            }
        }
    }
}
