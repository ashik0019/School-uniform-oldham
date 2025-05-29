<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\User;
use App\Models\PasswordReset;
use App\Models\VerificationCode;
use App\Notifications\PasswordResetRequest;
use App\Traits\ResponseAPI;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    use ResponseAPI;

    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user)
            return response()->json([
                'success' => false,
                'message' => 'We can not find a user with that e-mail address'
            ], 404);

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Str::random(60)
            ]
        );

        if ($user && $passwordReset)
            $user->notify(
                new PasswordResetRequest($passwordReset->token)
            );

        return response()->json([
            'success' => true,
            'message' => 'Please check your email. We have e-mailed your password reset link'
        ], 200);
    }

    public function forgetPassCreate(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|string',
                'new_password' => 'required|string|min:6',
                'otp' => 'required|string|min:4|max:4',
            ]);

            $user = User::where('phone', $request->phone)->first();
            if (!$user) {
                return $this->error('User does not match!', 404);
            }

            $check =  VerificationCode::where('phone', $request->phone)->latest('id')->first();
            if (!empty($check) && $check->code != $request->otp) {
                return $this->error("Only Developer knows, what's happening there!", 500);
            }

            if ($user) {
                $user->password = Hash::make($request->new_password);
                $user->save();
                return $this->success('Password successfully changed!', [
                    'is_user_matched' => true,
                ], 200);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
