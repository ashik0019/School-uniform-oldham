<?php

/** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api;

use App\Helpers\UserInfo;
use App\User;
use App\Order;
use App\Wishlist;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\CartCollection;
use App\Models\VerificationCode;
use App\Notifications\EmailVerificationNotification;
use App\Traits\ResponseAPI;


class AuthController extends Controller
{
    use ResponseAPI;

    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6'
        ]);
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
            $user->email_verified_at = date('Y-m-d H:m:s');
        } else {
            $user->notify(new EmailVerificationNotification());
        }
        $user->save();

        $customer = new Customer;
        $customer->user_id = $user->id;
        $customer->save();
        return response()->json([
            'message' => 'Registration Successful. Please verify and log in to your account.'
        ], 201);
    }

    public function signupViaPhone(Request $request)
    {
        try {
            $existingUser = User::where('phone', $request->phone)->first();  // Check if the phone number already exists
            if (!empty($existingUser)) {
                return $this->error('This number already exists in our system. Try another one.', 409, [
                    'is_number_exist' => 1,
                    'is_verified' => $existingUser->is_verified,
                    'is_refer_valid' => 0
                ]);
            }

            $request->validate([ 
                'name' => 'required|string',
                'phone' => 'required',
                'password' => 'required|string|min:6',
            ]);


            $user = new User([ // Create a new user
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => bcrypt($request->password),
            ]);

            $randId = mt_rand(10000000, 99999999);  
            while (User::where('referral_code', $randId)->exists()) {
                $randId = mt_rand(10000000, 99999999);
            }

            if (empty($request->referral_code)) {  // Validate referral code
                $refCode = 11111111;
            } else {
                $referrer = User::where('referral_code', $request->referral_code)->first();
                if (empty($referrer)) {
                    return $this->error('Invalid referral ID. Please insert the correct one.', 400, [
                        'is_number_exist' => 0,
                        'is_verified' => 0,
                        'is_refer_valid' => 0
                    ]);
                }
                $refCode = $request->referral_code;
            }

            $user->referral_code = $randId; // Assign referral code and referred by code
            $user->referred_by = $refCode;

            if (!empty($referrer)) {  // Add balance if a valid referrer exists
                $user->balance = 0;
            }
            $user->save();

            $customer = new Customer();   // Create a new customer associated with the user
            $customer->user_id = $user->id;
            $customer->save();
            VerificationCode::where('phone', $request->phone)->delete(); // Delete any existing verification code for the phone number
            $verificationCode = new VerificationCode(); // Generate a new verification code
            $verificationCode->phone = $request->phone;
            $verificationCode->code = mt_rand(1111, 9999);
            $verificationCode->status = 0;
            $verificationCode->save();
            // Prepare the SMS message
            $text = "{$verificationCode->code} is your One-Time Password (OTP) for Life Okey. Enjoy and purchase with Life Okey.";
            UserInfo::smsAPI("88" . $verificationCode->phone, $text);

            // Return success response
            return $this->success('Registration successful. Please verify and log in to your account.', [
                'is_number_exist' => 0,
                'is_verified' => 0,
                'is_refer_valid' => !empty($referrer) ? 1 : 0,
            ]);
        } catch (\Exception $e) { // Error handling
            return $this->error('An error occurred during registration.', 500, [
                'error_message' => $e->getMessage()
            ]);
        }
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials))
            return response()->json(['message' => 'Unauthorized'], 401);
        $user = $request->user();
        if ($user->email_verified_at == null) {
            return response()->json(['message' => 'Please verify your account'], 401);
        }
        $tokenResult = $user->createToken('Personal Access Token');
        return $this->loginSuccess($tokenResult, $user);
    }

    public function mobileLogin(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|string',
                'password' => 'required|string',
            ]);
            $credentials = $request->only('phone', 'password');
            if (!Auth::attempt($credentials)) {
                return $this->error('Unauthorized', 401);
            }
            $user = $request->user();
            // if ($user->is_verified == 0) {  // Check if the user is verified
            //     return $this->error('Please verify your account', 403, [
            //         'is_verified' => 0
            //     ]);
            // }
            if ($request->fcm_token) {
                $user->fcm_token = $request->fcm_token;
                $user->save();
            }
            $tokenResult = $user->createToken('Personal Access Token'); // Generate a personal access token
            return $this->success('Login successful', $this->userData($tokenResult, $user));
            //$res = $this->userData($tokenResult, $user);
            //return $this->success('Login successful', ['user' => $res]);
        } catch (\Exception $e) {   // Error handling
            return $this->error('An error occurred during login.', 500, [
                'error_message' => $e->getMessage()
            ]);
        }
    }

    public function user(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return $this->error('User not authenticated.', 401);
            }
            return $this->success('User data retrieved successfully.', $user);
        } catch (\Exception $e) {
            return $this->error('An error occurred while retrieving user data.', 500, [
                'error_message' => $e->getMessage()
            ]);
        }
    }

    public function getBalance()
    {
        try {
            $user = Auth::user();
            $response['balance'] = $user->balance;
            return $this->success("Balance Data", $response);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
    public function getDashboardData()
    {
        try {
            $user = Auth::user();
            $response['balance'] = $user->balance;
            $response['totalOrders'] = Order::where('user_id', $user->id)->count();
            $response['totalWishlist'] = Wishlist::where('user_id', $user->id)->count();
            $response['totalCart'] = Cart::where('user_id', $user->id)->count();
            return $this->success("Dashboard Data", $response);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
            return $this->success('Successfully logged out', ['is_loggout' => 1], 200);
        } catch (\Exception $e) {
            return $this->error('Logout failed: ' . $e->getMessage(), 500);
        }
    }
    public function socialLogin(Request $request)
    {
        $request->validate([
            //'email' => 'required|string|email'
            'provider' => 'required'
        ]);
        //if (User::where('email', $request->email)->first() != null) {
        $userCheckData = User::where('provider_id', $request->provider)->where('provider_id', '!=', null)->first();

        if ($userCheckData != null) {
            $user = User::where('provider_id', $request->provider)->where('provider_id', '!=', null)->first();
            $tokenResult = $user->createToken('Personal Access Token');
            return $this->loginSuccess($tokenResult, $user);
        } else {
            $userEmail = null;
            $userPhone = null;
            if ($request->email) {
                $userEmail = User::where('email', $request->email)->first();
            }
            if ($userPhone) {
                $userPhone = User::where('phone', $request->phone)->first();
            }
            
            if ($userEmail != null) {
                $userEmail->provider_id = $request->provider;
                $userEmail->fcm_token = $request->fcm_token;
                $userEmail->save();
                $user = $userEmail;
            } elseif ($userPhone != null) {
                $userPhone->provider_id = $request->provider;
                $userPhone->fcm_token = $request->fcm_token;
                $userPhone->save();
                $user = $userPhone;
            } else {
                //dd($request->all());
                $user = new User([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'fcm_token' => $request->fcm_token,
                    'provider_id' => $request->provider,
                    'is_verified' => 1,
                    'email_verified_at' => Carbon::now()
                ]);
                $randId = mt_rand(10000000, 99999999);
                $check = User::where('referral_code', $randId)->first();
                if (!empty($check)) {
                    $randId = mt_rand(10000000, 99999999);
                }
                $user->referral_code = $randId;
                $user->referred_by = 11111111;
                $user->save();
                $customer = new Customer;
                $customer->user_id = $user->id;
                $customer->save();
            }
            $tokenResult = $user->createToken('Personal Access Token');
            return $this->loginSuccess($tokenResult, $user);
        }
    }

    protected function loginSuccess($tokenResult, $user)
    {
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(100);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'user' => [
                'id' => $user->id,
                'type' => $user->user_type,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'avatar_original' => $user->avatar_original,
                'address' => $user->address,
                'country'  => $user->country,
                'city' => $user->city,
                'postal_code' => $user->postal_code,
                'referral_code' => $user->referral_code,
                'referred_by' => $user->referred_by,
                'balance' => $user->balance,
                'is_verified' => $user->is_verified,
                'banned' => $user->banned,
                'cart' => new CartCollection(Cart::where('user_id', $user->id)->latest()->get())
            ]
        ]);
    }

    protected function userData($tokenResult, $user)
    {
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(100);
        $token->save();
        return [
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'user' => [
                'id' => $user->id,
                'type' => $user->user_type,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'avatar_original' => $user->avatar_original,
                'address' => $user->address,
                'country'  => $user->country,
                'city' => $user->city,
                'postal_code' => $user->postal_code,
                'referral_code' => $user->referral_code,
                'referred_by' => $user->referred_by,
                'balance' => $user->balance,
                'is_verified' => $user->is_verified,
                'banned' => $user->banned,
                'cart' => new CartCollection(Cart::where('user_id', $user->id)->latest()->get())
            ]
        ];
    }

    public function changePass(Request $request)
    {
        try {
            $user = User::find($request->user_id);
            if (empty($user)) {
                return $this->error('User not found!', 404, [
                    'is_old_pass_matched' => 0,
                    'is_user_matched' => 0
                ]);
            }
            if (!Hash::check($request->old_password, $user->password)) {
                return $this->error('Old password does not match!', 400, [
                    'is_old_pass_matched' => 0,
                    'is_user_matched' => 1
                ]);
            }
            $user->password = Hash::make($request->new_password);
            $user->save();
            return $this->success('Password successfully changed!', [
                'is_old_pass_matched' => 1,
                'is_user_matched' => 1
            ]);
        } catch (\Exception $e) {
            return $this->error('An error occurred while changing the password.', 500, [
                'error_message' => $e->getMessage()
            ]);
        }
    }
}
