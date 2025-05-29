<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\VerificationCode;
use App\Helpers\UserInfo;
use App\Traits\ResponseAPI;


class OtpVerificationController extends Controller
{
    use ResponseAPI;

    public function OtpSend(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|string|max:11', // Adjust max length as needed
            ]);

            VerificationCode::where('phone', $request->phone)->delete(); // Delete any existing verification code for the phone number
            $verificationCode = new VerificationCode(); // Generate a new verification code
            $verificationCode->phone = $request->phone;
            $verificationCode->code = mt_rand(1111, 9999);
            $verificationCode->status = 0;
            $verificationCode->save();
            // Prepare the SMS message
            $text = "{$verificationCode->code} is your One-Time Password (OTP) for Life Okey. Enjoy and purchase with Life Okey.";
            UserInfo::smsAPI("88" . $verificationCode->phone, $text); // Send the SMS using UserInfo's smsAPI method
            return $this->success('OTP successfully sent to user', [
                'is_otp_sent' => true,
            ], 201);
        } catch (\Exception $e) {
            return $this->error('Failed to send OTP: ' . $e->getMessage(), 500);
        }
    }

    public function OtpCheck(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|string|max:11', // Adjust max length as needed
                'code' => 'required|string|min:4|max:4', // OTP should be of specific length
                'isRegistration' => 'required', // Ensure isRegistration is provided
            ]);
            // Check for the verification code
            $verification = VerificationCode::where('phone', $request->phone)
                ->where('status', 0)
                ->where('code', $request->code)
                ->first();

            if (!empty($verification)) {
                // Update the verification status
                $verification->status = 1;
                $verification->save();

                // Update user details
                $user = User::where('phone', $request->phone)->first();
                $user->email_verified_at = Carbon::now();
                $user->banned = 0;
                $user->save();

                if ($request->isRegistration == 1) {
                    $toReferer = User::where('referral_code', $user->referred_by)->first();
                    if ($toReferer) {
                        $toReferer->balance += 11;
                        $toReferer->save();
                    }
                    $user->is_verified = 1;
                    $user->save();
                }

                // Return success response
                return $this->success('OTP Checked successfully', [  // Using your success response method
                    'is_otp_matched' => 1,
                ], 200);
            } else {
                return $this->error('OTP Code does not match!', 400);  // Using your error response method
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
