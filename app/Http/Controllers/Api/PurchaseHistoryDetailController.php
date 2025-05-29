<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PurchaseHistoryCollection;
use App\Http\Resources\PurchaseHistoryDetailCollection;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Traits\ResponseAPI;
use Carbon\Carbon;

class PurchaseHistoryDetailController extends Controller
{
    use ResponseAPI;
    public function index($id)
    {
        try {
            $data = Order::where('id', $id)->first();

            $order = [
                'id' => $data->id,
                'code' => $data->code,
                'delivery_status' => optional($data->orderDetails->first())->delivery_status,
                'user' => [
                    'name' => $data->user->name,
                    'email' => $data->user->email,
                    'avatar' => $data->user->avatar,
                    'avatar_original' => $data->user->avatar_original
                ],
                'shipping_address' => json_decode($data->shipping_address),
                'payment_type' => str_replace('_', ' ', $data->payment_type),
                'payment_status' => $data->payment_status,
                'grand_total' => (float) $data->grand_total,
                'coupon_discount' => (float) $data->coupon_discount,
                'shipping_cost' => (float) $data->delivery_charge,
                //'shipping_cost' => (float) $data->orderDetails->sum('shipping_cost'),
                'subtotal' => (float) $data->orderDetails->sum('price'),
                'tax' => (float) $data->orderDetails->sum('tax'),
                'date' => Carbon::createFromTimestamp($data->date)->format('d-m-Y'),

            ];


            $orderDetials = new PurchaseHistoryDetailCollection(OrderDetail::where('order_id', $id)->get());
            $data2["order"] = $order;
            $data2["products"] = $orderDetials;

            return $this->success("Successfully fetched purchase details", $data2);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
