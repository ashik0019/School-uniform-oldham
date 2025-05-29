<?php

/**
 * Created by PhpStorm.
 * User: ashiq
 * Date: 11/11/2019
 * Time: 3:08 PM
 */

namespace App\Helpers;

use App\Model\FlashDeal;
use App\Model\FlashDealProduct;
use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Auth;
use Auth;
use Session;
use Carbon\Carbon;
// use App\Helpers\UserInfo;
use Intervention\Image\ImageManagerStatic as Image;

class UserInfo
{
    public function __construct() {}


    // public static function smsAPI($receiver_number, $sms_text)
    // {


    //     $api = "https://api.mobireach.com.bd/SendTextMessage?Username=lifeok&Password=Dhaka@5599&From=".urlencode("LifeOk Shop")."&To=".$receiver_number."&Message=". urlencode($sms_text);
    //     //$api ="http://isms.zaman-it.com/smsapi?api_key=C20000365d831ca2c90451.06457950&type=text&contacts=".$receiver_number."&senderid=8809612451614&msg=".urlencode($sms_text);

    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => $api,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => "",
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 30,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => "GET",
    //         CURLOPT_HTTPHEADER => array(
    //             "accept: application/json",
    //             "authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ=="
    //         ),
    //     ));
    //     //dd($curl);
    //     $response = curl_exec($curl);
    //     $err = curl_error($curl);

    //     curl_close($curl);

    //     if ($err) {
    //         return $err;
    //     } else {
    //         return $response;
    //     }
    // }


    public static function smsAPI($receiver_number, $sms_text)
    {
        $token = "c5rys3ae-5lemceo5-9h8exekk-ojle6gte-830ed0sw";
        $sid = "LIFEOKEY"; 
       $DOMAIN =  "https://smsplus.sslwireless.com";
        $params = [
            "api_token" => $token,
            "sid" => $sid,
            "msisdn" => $receiver_number,
            "sms" => $sms_text,
            "csms_id" => date('dmYhhmi') . rand(10000, 99999)
        ];

       // $url = env("SSL_SMS_URL");
       $url = trim($DOMAIN, '/')."/api/v3/send-sms";
        $params = json_encode($params);

        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($params),
            'accept:application/json'
        ));
    
        $response = curl_exec($ch);
    
        curl_close($ch);
    
        return $response;
    }
}
