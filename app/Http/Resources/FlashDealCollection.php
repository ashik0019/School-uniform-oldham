<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\ProductCollection;
use App\Models\FlashDeal;
use App\Models\Product;

class FlashDealCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Get the first flash deal in the collection
        $flash_deal = FlashDeal::findOrFail($this->collection->first()->id);

        // Fetch paginated products related to the flash deal
        $flash_deal_product_ids = $flash_deal->flashDealProducts->pluck('product_id');
        $paginated_products = Product::whereIn('id', $flash_deal_product_ids)
                                     ->paginate(10); // Adjust the number of items per page as needed

        // Transform the response
        return [
            'title' => $flash_deal->title,
            'status' => $flash_deal->status,
            'start_date' => $flash_deal->start_date,
            'end_date' => $flash_deal->end_date,
            'products' => new FilterProductCollection($paginated_products),
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200,
        ];
    }
}
