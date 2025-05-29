<?php

namespace App\Http\Controllers\Api;

use App\Helpers\UserInfo;
use App\Http\Controllers\Controller;
use App\Models\FCMToken;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;


class FcmTokenController extends Controller
{
    use ResponseAPI;
    public function storeUnsigned(Request $request)
    {
        try {
            $fcm = new FCMToken();
            if ($request->token) {
                $exist = FCMToken::where('token', $request->token)->first();
                if (!$exist) {
                    $fcm->token = $request->token;
                    $fcm->status = 1;
                    $fcm->save();
                }
            }
            return $this->success("Successfully Store Unsigned Token", $fcm);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
    public function storeSigned(Request $request)
    {
        try {

            if ($request->token) {
                $exist = FCMToken::where('token', $request->token)->first();
                $oldData = FCMToken::where('user_id', $request->user_id)->first();
                $oldData->delete();
                if (!$exist) {
                    $fcm = new FCMToken();
                    $fcm->token = $request->token;
                    $fcm->status = 1;
                    $fcm->user_id = $request->user_id;
                    $fcm->user_type = 'signed';
                    $fcm->save();
                } else {
                    $exist->token = $request->token;
                    $exist->status = 1;
                    $exist->user_id = $request->user_id;
                    $exist->user_type = 'signed';
                    $exist->save();
                }
            }
            return $this->success("Successfully Store Signed Token", []);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
    public function sendOtp(Request $request)
    {
        try {

           $res =  UserInfo::smsAPI('01723144515','testing');
            
            return $this->success("Successfully sent otp Signed Token", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
