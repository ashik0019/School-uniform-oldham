<?php

namespace App\Http\Controllers;

use App\Helpers\UserInfo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ForgotPasswordController extends Controller
{
    public function resetPass()
    {
        return view('frontend.forget_pass_check');
    }
    public function phoneCheck(Request $request)
    {
        $userData = User::where('phone',$request->phone)->first();
        if (!empty($userData))
        {
            $otp = mt_rand(1111, 9999);
            Session::put('otp',$otp);
            Session::put('userId',$userData->id);
            flash(__('Please check your phone inbox to get OTP'))->success();
            $text = "Dear, ".$userData->name.", Your forget password OTP code is ".$otp."\nwww.lifeokshop.com";
            UserInfo::smsAPI("88".$userData->phone,$text);
            return redirect()->route('reset.pass.otp-form');
        }else{
            flash(__("Your entered phone number doesn't match with our system" ))->error();
            //dd('else');
            return redirect()->back();
        }
    }
    public function otpForm()
    {
        return view('frontend.otp_check');
    }
    public function otpCheck(Request $request)
    {
        $otp = Session::get('otp');
        if($otp == $request->otp)
        {
            flash(__('Your entered OTP successfully match.Thanks!'))->success();
            return redirect()->route('reset.pass.newpass-form');
        }else{
            flash(__("Your entered OTP doesn't match.Thanks!"))->error();
            return redirect()->back();
        }
    }
    public function newPassForm()
    {
        return view('frontend.reset_pass');
    }
    public function newPassStore(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|confirmed|min:6',
        ]);
        $userId = Session::get('userId');
        $user = User::find($userId);
        $user->password = Hash::make($request->password);
        $user->save();
        flash(__('Your new password successfully changed.Thanks!'))->success();
        Session::forget('otp');
        Session::forget('userId');
        return redirect()->route('user.login');
    }
}
