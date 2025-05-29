<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PurchaseHistoryCollection;
use App\Models\Order;
use App\Traits\ResponseAPI;

class PurchaseHistoryController extends Controller
{
    use ResponseAPI;

    public function index($id)
    {
        try {
            $orderData =  new PurchaseHistoryCollection(Order::where('user_id', $id)->latest()->get());
            return $this->success("Successfully fetched purchase history", $orderData);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
       
    }
}
