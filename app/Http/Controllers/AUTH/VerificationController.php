<?php

namespace App\Http\Controllers\AUTH;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetEmail;
use Illuminate\Http\Request;
use App\Mail\VerificationEmail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
            return response()->json([
                'errors' => $validate->errors(),
                'status'=>false
                ]);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $verificationCode = rand(100000, 999999); // Generate a random six digit code
            $user->verificationCode = $verificationCode;
            $user->verification_code_expires_at = now()->addMinutes(5);
            $user->save();

            Mail::to($user->email)->send(new VerificationEmail($user));

            return response()->json([
                'message' => 'Verification email sent.',
                'status'=>true
            ]);
        } else {
            return response()->json([
                'message' => 'User not found.',
                'status'=>false
            ]);
        }
    }

    public function verify(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'verificationCode' => 'required|numeric',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors(),
                'status'=>false
            ]);
        }

        $user = User::where('email', $request->email)->first();


        if ($user) {
            if ($user->verificationCode == $request->verificationCode) {
                if ($user->verification_code_expires_at > now()) {
                    $user->email_verified_at = now();
                    $user->verificationCode = null;
                    $user->save();

                    return response()->json([
                        'message' => 'Email verified successfully.',
                        'status'=>true
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Verification code expired.',
                        'status'=>false
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Invalid verification code.',
                    'status'=>false
                ]);
            }
        }
    }



    public function sendPasswordResetEmail(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors(),
                'status'=>false
            ]);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $resetToken = rand(100000, 999999); // Generate a random six digit code
            $user->resetToken = $resetToken;
            $user->reset_token_expires_at = now()->addMinutes(5);
            $user->save();

            Mail::to($user->email)->send(new PasswordResetEmail($user));

            return response()->json([
                'message' => 'Password reset email sent.',
                'status'=>true
            ]);
        } else {
            return response()->json([
                'message' => 'User not found.',
                'status'=>false
            ]);
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
            return response()->json([
                'errors' => $validate->errors(),
                'status'=>false
            ]);
        }

        $user = User::where('email', $request->email)->first();


        if ($user) {
            if ($user->resetToken == $request->resetToken) {
                if ($user->reset_token_expires_at > now()) {
                    $user->password = bcrypt($request->password);
                    $user->resetToken = null;
                    $user->save();

                    return response()->json([
                        'message' => 'Password reset successful.',
                        'status'=>true
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Reset token expired.',
                        'status'=>false
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Invalid reset token.',
                    'status'=>false
                ]);
            }
        }
    }


    public function changePassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors(),
                'status'=>false
            ]);
        }

        $user = $request->user(); // Get the currently authenticated user

        if ($user) {
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = bcrypt($request->new_password);
                $user->save();

                return response()->json([
                    'message' => 'Password changed successfully.',
                    'status'=>true
                ]);
            } else {
                return response()->json([
                    'message' => 'Current password is incorrect.',
                    'status'=>false
                ]);
            }
        }
    }
}
