<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\WishlistCollection;
use App\Models\Wishlist;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    use ResponseAPI;

    public function index($id)
    {

        try {
            $wishlistData =  new WishlistCollection(Wishlist::where('user_id', $id)->latest()->get());
            return $this->success("Successfully fetched wishlist", $wishlistData);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function store(Request $request)
    {
        try {
            Wishlist::updateOrCreate(
                ['user_id' => $request->user_id, 'product_id' => $request->product_id]
            );
            $wishlistData =  new WishlistCollection(Wishlist::where('user_id', $request->user_id)->latest()->get());
            return $this->success("Product is successfully added to your wishlist", $wishlistData);
            //return response()->json(['message' => 'Product is successfully added to your wishlist'], 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        try {
            $item = Wishlist::destroy($id);
            return $this->success('Product is successfully removed from your wishlist', ['msg'=> 'id: '.$id.' is deleted']);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function isProductInWishlist(Request $request)
    {
        try {
            $product = Wishlist::where(['product_id' => $request->product_id, 'user_id' => $request->user_id])->count();
            if ($product > 0) {
                $res = [
                    // 'message' => 'Product present in wishlist',
                    'is_in_wishlist' => true,
                    'product_id' => (int) $request->product_id,
                    'wishlist_id' => (int) Wishlist::where(['product_id' => $request->product_id, 'user_id' => $request->user_id])->first()->id
                ];
                return $this->success("Product present in wishlist", $res);
            }

            $notREs = [
                // 'message' => 'Product is not present in wishlist',
                'is_in_wishlist' => false,
                'product_id' => (int) $request->product_id,
                'wishlist_id' => 0
            ];
            return $this->success("Product is not present in wishlist", $notREs);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
