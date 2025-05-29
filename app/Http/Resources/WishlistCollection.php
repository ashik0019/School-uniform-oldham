<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WishlistCollection extends ResourceCollection
{
    public function toArray($request)
    {
        //dd($request);
        return [
            'data' => $this->collection->map(function($data) {
                //dd($data);
                return [
                    'id' => (integer) $data->id,
                    'product' => [
                        'id' => !empty($data->product) ? (integer) $data->product->id : 0,
                        'name' => !empty($data->product) ? $data->product->name : 'N/A',
                        'thumbnail_image' => !empty($data->product) ?  $data->product->thumbnail_img : '',
                        'base_price' => !empty($data->product) ?  (double) homeBasePrice($data->product->id) : 0,
                        'base_discounted_price' => !empty($data->product) ?  (double) homeDiscountedBasePrice($data->product->id) : 0,
                        'unit' => !empty($data->product) ? $data->product->unit : 'N/a',
                        'rating' => !empty($data->product) ? (double) $data->product->rating : 0,
                        'links' => [
                            'details' => route('products.show', !empty($data->product) ?  $data->product->id : ''),
                            'reviews' => route('api.reviews.index', !empty($data->product) ?  $data->product->id : ''),
                            'related' => route('products.related', !empty($data->product) ?  $data->product->id : ''),
                        ]
                    ]
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
