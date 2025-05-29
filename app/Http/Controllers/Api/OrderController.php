<?php

namespace App\Http\Controllers\Api;

use App\Helpers\UserInfo;
use App\Http\Resources\PurchaseHistoryDetailCollection;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\BusinessSetting;
use App\Traits\ResponseAPI;
use App\User;
use DB;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderController extends Controller
{
    use ResponseAPI;
    public function processOrder(Request $request)
    {
        try {
            //return $request->all();
            $shippingAddress = json_decode($request->shipping_address);
            $cartItems = Cart::where('user_id', $request->user_id)->get();
            $shipping = 0;
            $admin_products = array();
            $seller_products = array();
            //

            if (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'flat_rate') {
                $shipping = \App\BusinessSetting::where('type', 'flat_rate_shipping_cost')->first()->value;
            } elseif (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'seller_wise_shipping') {
                foreach ($cartItems as $cartItem) {
                    $product = \App\Product::find($cartItem->product_id);
                    if ($product->added_by == 'admin') {
                        array_push($admin_products, $cartItem->product_id);
                    } else {
                        $product_ids = array();
                        if (array_key_exists($product->user_id, $seller_products)) {
                            $product_ids = $seller_products[$product->user_id];
                        }
                        array_push($product_ids, $cartItem->product_id);
                        $seller_products[$product->user_id] = $product_ids;
                    }
                }
                if (!empty($admin_products)) {
                    $shipping = \App\BusinessSetting::where('type', 'shipping_cost_admin')->first()->value;
                }
                if (!empty($seller_products)) {
                    foreach ($seller_products as $key => $seller_product) {
                        $shipping += \App\Shop::where('user_id', $key)->first()->shipping_cost;
                    }
                }
            }

            // create an order
            $order = Order::create([
                'user_id' => $request->user_id,
                'shipping_address' => json_encode($shippingAddress),
                'payment_type' => $request->payment_type,
                'delivery_charge' => $request->delivery_charge,
                'payment_status' => $request->payment_status,
                'grand_total' => $request->grand_total + $shipping,    //// 'grand_total' => $request->grand_total + $shipping,
                //'coupon_discount' => $request->coupon_discount,
                'coupon_discount' => 0,
                'code' => date('Ymd-his'),
                'date' => strtotime('now')
            ]);

            foreach ($cartItems as $cartItem) {
                $product = Product::findOrFail($cartItem->product_id);
                if ($cartItem->variation) {
                    $cartItemVariation = $cartItem->variation;
                    $product_stocks = $product->stocks->where('variant', $cartItem->variation)->first();
                    $product_stocks->qty -= $cartItem->quantity;
                    $product_stocks->save();
                } else {
                    $product->update([
                        'current_stock' => DB::raw('current_stock - ' . $cartItem->quantity)
                    ]);
                }

                $order_detail_shipping_cost = 0;

                if (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'flat_rate') {
                    $order_detail_shipping_cost = $shipping / count($cartItems);
                } elseif (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'seller_wise_shipping') {
                    if ($product->added_by == 'admin') {
                        $order_detail_shipping_cost = \App\BusinessSetting::where('type', 'shipping_cost_admin')->first()->value / count($admin_products);
                    } else {
                        $order_detail_shipping_cost = \App\Shop::where('user_id', $product->user_id)->first()->shipping_cost / count($seller_products[$product->user_id]);
                    }
                } else {
                    $order_detail_shipping_cost = $product->shipping_cost;
                }

                // save order details
                OrderDetail::create([
                    'order_id' => $order->id,
                    'seller_id' => $product->user_id,
                    'product_id' => $product->id,
                    'variation' => $cartItem->variation,
                    'price' => $cartItem->price * $cartItem->quantity,
                    'tax' => $cartItem->tax * $cartItem->quantity,
                    'shipping_cost' => $order_detail_shipping_cost,
                    'quantity' => $cartItem->quantity,
                    'payment_status' => $request->payment_status
                ]);
                $product->update([
                    'num_of_sale' => DB::raw('num_of_sale + ' . $cartItem->quantity)
                ]);
            }
            $user = User::find($request->user_id);
            //if ($request->payment_option == 'wallet') {
            if ($request->payment_type == 'wallet') {
                $user->balance -= $order->grand_total;
                $user->save();
            }
            if ($request->payment_type != 'sslcommerz') {
                $text = "You have placed an order to Life Okey. \nYour order ID: " . $order->code . " and Purchased Grand Total Amount: " . $order->grand_total . "tk. Thank You." . "\n Stay with www.lifeokshop.com";
                UserInfo::smsAPI("88" . $user->phone, $text);
            }
            // apply coupon usage
            /* if ($request->coupon_code != '') {
            CouponUsage::create([
                'user_id' => $request->user_id,
                'coupon_id' => Coupon::where('code', $request->coupon_code)->first()->id
            ]);
        }*/
            // calculate commission
            $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
            foreach ($order->orderDetails as $orderDetail) {
                if ($orderDetail->product->user->user_type == 'seller') {
                    $seller = $orderDetail->product->user->seller;
                    $price = $orderDetail->price + $orderDetail->tax + $orderDetail->shipping_cost;
                    $seller->admin_to_pay = ($request->payment_type == 'cash_on_delivery') ? $seller->admin_to_pay - ($price * $commission_percentage) / 100 : $seller->admin_to_pay + ($price * (100 - $commission_percentage)) / 100;
                    $seller->save();
                }
            }
            // clear user's cart
            $user = User::findOrFail($request->user_id);
            $user->carts()->delete();

            // return response()->json([
            //     'success' => true,
            //     'order_id' => $order->id,
            //     'message' => 'Your order has been placed successfully'
            // ]);
            return $this->success("Your order has been placed successfully", ['order_id' => $order->id]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function store(Request $request)
    {
        return $this->processOrder($request);
    }

    public function orderCancel($orderId)
    {
        $order = Order::find($orderId);
        foreach ($order->orderDetails as $key => $orderDetail) {
            $orderDetail->delivery_status = 'cancel';
            $orderDetail->save();
        }
        return response()->json([
            'success' => true,
            'message' => 'Order Successfully Canceled'
        ]);
    }

    public function trackOrder($order_code)
    {
        try {
            if (!empty('order_code')) {
                $order = Order::where('code', $order_code)->first();
                $orderDetails = OrderDetail::where('order_id', $order->id)->first();
                $order['details'] = new PurchaseHistoryDetailCollection(OrderDetail::where('order_id', $order->id)->get());

                if ($order != null) {
                    return $this->success("Successfully fetched order", $order);
                }
            }
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Your order Does not match in our system.'
            // ], 401);
            return $this->error("Your order Does not match in our system.", 401);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
