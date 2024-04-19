<?php

namespace App\Http\Controllers\AUTH;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetEmail;
use Illuminate\Http\Request;
use App\Mail\VerificationEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{

    public function sendVerificationEmail(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 400);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $verificationCode = rand(100000, 999999); // Generate a random six digit code
            $user->verificationCode = $verificationCode;
            $user->verification_code_expires_at = now()->addMinutes(5);
            $user->save();

            Mail::to($user->email)->send(new VerificationEmail($user));

            return response()->json(['message' => 'Verification email sent.']);
        } else {
            return response()->json(['message' => 'User not found.'], 404);
        }
    }

    public function verify(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'verificationCode' => 'required|numeric',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 400);
        }

        $user = User::where('email', $request->email)->first();


        if ($user) {
            if ($user->verificationCode == $request->verificationCode) {
                if ($user->verification_code_expires_at > now()) {
                    $user->email_verified_at = now();
                    $user->verificationCode = null;
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



    public function sendPasswordResetEmail(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 400);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $resetToken = rand(100000, 999999); // Generate a random six digit code
            $user->resetToken = $resetToken;
            $user->reset_token_expires_at = now()->addMinutes(5);
            $user->save();

            Mail::to($user->email)->send(new PasswordResetEmail($user));

            return response()->json(['message' => 'Password reset email sent.']);
        } else {
            return response()->json(['message' => 'User not found.'], 404);
        }
    }

    public function resetPassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'resetToken' => 'required|numeric',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 400);
        }

        $user = User::where('email', $request->email)->first();


        if ($user) {
            if ($user->resetToken == $request->resetToken) {
                if ($user->reset_token_expires_at > now()) {
                    $user->password = bcrypt($request->password);
                    $user->resetToken = null;
                    $user->save();

                    return response()->json(['message' => 'Password reset successful.']);
                } else {
                    return response()->json(['message' => 'Reset token expired.'], 400);
                }
            } else {
                return response()->json(['message' => 'Invalid reset token.'], 400);
            }
        }
    }

}
