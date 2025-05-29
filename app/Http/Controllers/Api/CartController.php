<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CartCollection;
use App\Models\Cart;
use App\Models\Color;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    use ResponseAPI;
    public function index($id)
    {
        try {
            $res = new CartCollection(Cart::where('user_id', $id)->latest()->get());
            return $this->success("Successfully fetched Cart", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function add(Request $request)
    {
        try {
            $product = Product::findOrFail($request->id);

            $variant = $request->variant;
            $color = $request->color;
            $tax = 0;

            if ($variant == '' && $color == '')
                $price = $product->unit_price;
            else {
                //$variations = json_decode($product->variations);
                $product_stock = $product->stocks->where('variant', $variant)->first();
                $price = $product_stock->price;
            }

            //discount calculation based on flash deal and regular discount
            //calculation of taxes
            $flash_deals = FlashDeal::where('status', 1)->get();
            $inFlashDeal = false;
            foreach ($flash_deals as $flash_deal) {
                if ($flash_deal != null && $flash_deal->status == 1  && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first() != null) {
                    $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first();
                    if ($flash_deal_product->discount_type == 'percent') {
                        $price -= ($price * $flash_deal_product->discount) / 100;
                    } elseif ($flash_deal_product->discount_type == 'amount') {
                        $price -= $flash_deal_product->discount;
                    }
                    $inFlashDeal = true;
                    break;
                }
            }
            if (!$inFlashDeal) {
                if ($product->discount_type == 'percent') {
                    $price -= ($price * $product->discount) / 100;
                } elseif ($product->discount_type == 'amount') {
                    $price -= $product->discount;
                }
            }

            if ($product->tax_type == 'percent') {
                $tax = ($price * $product->tax) / 100;
            } elseif ($product->tax_type == 'amount') {
                $tax = $product->tax;
            }

            Cart::updateOrCreate([
                'user_id' => $request->user_id,
                'product_id' => $request->id,
                'variation' => $variant
            ], [
                'price' => $price,
                'tax' => $tax,
                'shipping_cost' => 0,
                //'quantity' => DB::raw('quantity + 1')
                'quantity' => $request->quantity
            ]);

            // return response()->json([
            //     'message' => 'Product added to cart successfully'
            // ]);
            $res = new CartCollection(Cart::where('user_id', Auth::user()->id)->latest()->get());
            return $this->success("Successfully Cart Added", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function changeQuantity(Request $request)
    {
        try {
            $cart = Cart::findOrFail($request->id);
            $cart->update([
                'quantity' => $request->quantity
            ]);
            // return response()->json(['message' => 'Cart updated'], 200);
            $res = new CartCollection(Cart::where('user_id', Auth::user()->id)->latest()->get());
            return $this->success("Successfully Cart updated", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        try {
            Cart::destroy($id);
            $res = new CartCollection(Cart::where('user_id', Auth::user()->id)->latest()->get());
            return $this->success("Product is successfully removed from your cart", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
