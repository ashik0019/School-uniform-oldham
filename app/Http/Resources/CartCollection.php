<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CartCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $stockQty = $data->variation 
                ? optional($data->product->stocks()->where('variant', $data->variation)->first())->qty 
                : $data->product->current_stock;
                return [
                    'id' => $data->id,
                    'product' => [
                        'id' => $data->product->id,
                        'name' => $data->product->name,
                        'image' => $data->product->thumbnail_img,
                        'stock_qty' => (int) $stockQty,
                    ],
                    'variation' => $data->variation,
                    'price' => (float) $data->price,
                    'tax' => (float) $data->tax,
                    'shipping_cost' => (float) $data->shipping_cost,
                    'quantity' => (int) $data->quantity,
                    'date' => $data->created_at->diffForHumans()
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
