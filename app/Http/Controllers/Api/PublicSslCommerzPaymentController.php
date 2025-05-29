<?php

namespace App\Http\Controllers\Api;



use App\Helpers\UserInfo;
use App\Order;
use App\OrderDetail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Redirect;
//use Session;
use Illuminate\Support\Facades\Session;
use Lang;
//use Illuminate\Routing\UrlGenerator;
use App\Http\Controllers\Controller;
use App\Traits\ResponseAPI;

session_start();

class PublicSslCommerzPaymentController extends Controller
{
    use ResponseAPI;

    public function index(Request $request)
    {

        try {
            $order =  Order::where('id', $request->order_id)->firstOrFail();
            $grand_total = $order->grand_total;
            $total = $grand_total;
            # Here you have to receive all the order data to initate  payment.
            # Lets your oder trnsaction informations are saving in a table called "orders"
            # In orders table order uniq identity is "order_id","ssl_status" field contain status of the transaction, "grand_total" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.
            $post_data = array();
            $post_data['total_amount'] = $total; # You cant not pay less than 10
            $post_data['currency'] = "BDT";
            $post_data['tran_id'] = $order->code; // tran_id must be unique
            Order::where('id', $request->order_id)->update(
                array(
                    /*'currency' => $post_data['currency'],*/
                    'code' => $post_data['tran_id'],
                    'payment_type' => 'sslcommerz',
                    'ssl_status' => 'Pending',
                )
            );

            #Start to save these value  in session to pick in success page.
            $_SESSION['payment_values']['tran_id'] = $post_data['tran_id'];
            #End to save these value  in session to pick in success page.
            //dd($_SESSION['payment_values']['tran_id']);
            //        $server_name=$request->root()."/";
            $server_name = url('/');
            $post_data['success_url'] = $server_name . "/api/success";
            $post_data['fail_url'] = $server_name . "/api/fail";
            $post_data['cancel_url'] = $server_name . "/api/cancel";

            #Before  going to initiate the payment order status need to update as Pending.
            $sslc = new SSLCommerz();
            # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
            $payment_options = $sslc->initiate($post_data, true);
            ///        dd($payment_options);

            if (!is_array($payment_options)) {
                // dd($payment_options);
                return response()->json([
                    'message' => 'SSL payment successfully generated',
                    'success' => true,
                    'status_code' => 200,
                    'result' => [
                        'payment_url' => $payment_options
                    ],

                ]);
                // print_r($payment_options);
                $payment_options = array();
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function success(Request $request)
    {

        $sslc = new SSLCommerz();
        #Start to received these value from session. which was saved in index function.
        $tran_id = $request->tran_id;
        #End to received these value from session. which was saved in index function.
        #Check order status in order tabel against the transaction id or order id.
        $order_detials = DB::table('orders')
            ->where('code', $tran_id)
            ->select('code', 'ssl_status', 'grand_total')->first();

        //$chekTotal= $order_detials->grand_total + number_format(Session::get('delivery_cost'),2);
        $chekTotal = $order_detials->grand_total;

        if ($order_detials->ssl_status == 'Pending') {
            $validation = $sslc->orderValidate($tran_id, $chekTotal, 'BDT', $request->all());
            if ($validation == TRUE) {
                /*
                That means IPN did not work or IPN URL was not set in your merchant panel. Here you need to update order status
                in order table as Processing or Complete.
                Here you can also sent sms or email for successfull transaction to customer
                */
                //                $update_product = DB::table('orders')
                //                    ->where('code', $tran_id)
                //                    ->update([
                //                        'ssl_status' => 'Completed',
                //                        'payment_status' => 'paid',
                //                        /*'payment_details' => json_encode($_POST),*/
                //                    ]);
                $update_product = Order::where('code', $tran_id)->first();
                $update_product->ssl_status = 'Completed';
                $update_product->payment_status = 'paid';
                $update_product->payment_details = json_encode($_POST);
                $update_product->save();
                //dd($update_product);
                $orderDetails = OrderDetail::where('order_id', $update_product->id)->get();
                foreach ($orderDetails as $key => $orderDetail) {
                    $orderDetail->payment_status = 'paid';
                    $orderDetail->save();
                }

                //                $order=Order::where('transaction_id', $tran_id)->first();
                //                $commission = OrderCommission::first();
                //                $commissionValue = $order->grand_total*$commission->commission_percentage / 100;
                //                DB::table('payment_histories')->insert([
                //                    'order_id' => $order->id,
                //                    'payment_type' => 'ssl',
                //                    'vendor_amount'=>$order->grand_total - $commissionValue,
                //                    'admin_amount'=>$commissionValue,
                //                ]);
                //Toastr::success('Transaction is successfully Completed tar','Success');
                //Cart::destroy();
                //Session::forget('delivery_cost');
                //                $message = Lang::get("website.Payment has been processed successfully");
                //                return redirect('/');
                $user = User::find($update_product->user_id);
                $decodedAddress = json_decode($update_product->shipping_address, true);
                $phoneNumber = $decodedAddress['phone'] ?? null;
                $text = "You have placed an order to Life Okey. \nYour order ID: " . $update_product->code . " and Purchased Grand Total Amount: " . $update_product->grand_total . "tk. Thank You." . "\n Stay with www.lifeokshop.com";
                UserInfo::smsAPI("88" . $phoneNumber, $text);
               // return 'success';
               return redirect('api/ssl/redirect/success?payment_status=success');
             //return redirect('api/ssl/redirect/success');
            } else {
                    /*
                That means IPN did not work or IPN URL was not set in your merchant panel and Transation validation failed.
                Here you need to update order status as Failed in order table.
                */;
                /*$update_product = DB::table('orders')
                    ->where('code', $tran_id)
                    ->update([
                        'coupon_discount' => $this->oldDiscount,
                        'grand_total' => $this->oldTotal
                    ]);*/

                return redirect('api/ssl/redirect/fail?payment_status=fail');
                //return redirect('api/ssl/redirect/fail');
            }
        } else if ($order_detials->ssl_status == 'Processing' || $order_detials->ssl_status == 'Complete') {
            /*
             That means through IPN Order status already updated. Now you can just show the customer that transaction is completed. No need to udate database.
             */
            /* $update_product = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->update([
                    'coupon_discount' => $this->oldDiscount,
                    'grand_total' => $this->oldTotal
                ]);*/
            return redirect('api/ssl/redirect/fail?payment_status=fail');
           // return redirect('api/ssl/redirect/fail');
        } else {
            #That means something wrong happened. You can redirect customer to your product page.
            /*  $update_product = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->update([
                    'coupon_discount' => $this->oldDiscount,
                    'grand_total' => $this->oldTotal
                ]);*/
            return redirect('api/ssl/redirect/fail?payment_status=fail');
            //return redirect('api/ssl/redirect/fail');
        }
    }
    public function fail(Request $request)
    {
        //dd($_SESSION['oldtotal']);
        $tran_id = $request->tran_id;
        $order_detials = DB::table('orders')
            ->where('code', $tran_id)
            ->select('id', 'ssl_status', 'grand_total')->first();

        if ($order_detials->ssl_status == 'Pending') {

            //dd($_POST);
            $update_product = DB::table('orders')
                ->where('code', $tran_id)
                ->update(['ssl_status' => 'Failed']);
            $update_product = Order::where('code', $tran_id)->first();
            $update_product->delete();
            //return redirect('api/ssl/redirect/fail');
            return redirect('api/ssl/redirect/fail?payment_status=fail');
        } else if ($order_detials->ssl_status == 'Processing' || $order_detials->ssl_status == 'Complete') {
            return redirect('api/ssl/redirect/success?payment_status=success');
        } else {
            $update_product = DB::table('orders')
                ->where('code', $tran_id)
                ->update(['ssl_status' => 'Failed']);
            //return redirect('api/ssl/redirect/fail');
            return redirect('api/ssl/redirect/fail?payment_status=fail');
        }
    }

    public function cancel(Request $request)
    {
        $tran_id = $tran_id = $request->tran_id;
        $order_detials = DB::table('orders')
            ->where('code', $tran_id)
            ->select('id', 'ssl_status', 'grand_total')->first();
        //dd($order_detials);
        if ($order_detials->ssl_status == 'Pending') {
            $update_product = Order::where('code', $tran_id)->first();
            $update_product->delete();

            //            $order_detials = OrderDetail::where('order_id', $update_product->id)->get();
            //            foreach ($order_detials as $order_detial){
            //                $details = OrderDetail::find($order_detial->id);
            //                $details->delivery_status ='cancel';
            //                $details->save();
            //            }
            return redirect('api/ssl/redirect/cancel?payment_status=cancel');
            //return "Transaction is Cancel go back to app";
        } else if ($order_detials->ssl_status == 'Processing' || $order_detials->ssl_status == 'Complete') {
            //            echo "Transaction is already Successful";
            //return redirect('api/ssl/redirect/success');
            return redirect('api/ssl/redirect/success?payment_status=success');
        } else {
            /* $update_product = DB::table('orders')
                ->where('code', $tran_id)
                ->update(['coupon_discount' => $_SESSION['olddiscount'],
                    'grand_total' => $_SESSION['oldtotal']]);*/
                return redirect('api/ssl/redirect/cancel?payment_status=cancel');
        }
    }
    public function ipn(Request $request)
    {
        #Received all the payement information from the gateway
        if ($request->input('tran_id')) #Check transation id is posted or not.
        {

            $tran_id = $request->input('tran_id');

            #Check order status in order tabel against the transaction id or order id.
            $order_details = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->select('id', 'ssl_status', 'currency', 'grand_total')->first();

            if ($order_details->ssl_status == 'Pending') {
                $sslc = new SSLCommerz();
                $validation = $sslc->orderValidate($tran_id, $order_details->grand_total, $order_details->currency, $request->all());
                if ($validation == TRUE) {
                    /*
                     *
                    That means IPN worked. Here you need to update order status
                    in order table as Processing or Complete.
                    Here you can also sent sms or email for successfull transaction to customer
                    */
                    $update_product = DB::table('orders')
                        ->where('transaction_id', $tran_id)
                        ->update(['ssl_status' => 'Complete']);
                    return redirect('api/ssl/redirect/success?payment_status=success');
                    echo "Transaction is successfully Complete";
                } else {
                    /*
                    That means IPN worked, but Transation validation failed.
                    Here you need to update order status as Failed in order table.
                    */
                    $update_product = DB::table('orders')
                        ->where('order_id', $tran_id)
                        ->update([
                            'ssl_status' => 'Failed',
                            'coupon_discount' => $_SESSION['olddiscount'],
                            'grand_total' => $_SESSION['oldtotal']
                        ]);
                    return redirect('api/ssl/redirect/fail?payment_status=fail');
                  //  echo "validation Fail";
                }
            } else if ($order_details->ssl_status == 'Processing' || $order_details->ssl_status == 'Complete') {
                #That means Order status already updated. No need to udate database.
                return redirect('api/ssl/redirect/success?payment_status=success');
               // echo "Transaction is already successfully Complete";
            } else {
                return redirect('api/ssl/redirect/fail?payment_status=fail');
                //echo "Invalid Transaction";
            }
        } else {
            return redirect('api/ssl/redirect/fail?payment_status=fail');
            //echo "Inavalid Data";
        }
    }
    public function status($status)
    {
        return view("status", compact('status'));
       // $url = '/ssl-commerz/redirect/'.$status.'?q='.$status;
       // return url($url);
    }
    public function statusWeb($status)
    {
        return view("status", compact('status'));
    }
}
