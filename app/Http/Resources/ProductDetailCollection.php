<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Review;
use App\Models\Attribute;

class ProductDetailCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    'id' => (integer) $data->id,
                    'name' => $data->name,
                    'added_by' => $data->added_by,
                    'user' => [
                        'name' => $data->user->name,
                        'email' => $data->user->email,
                        'avatar' => $data->user->avatar,
                        'avatar_original' => $data->user->avatar_original,
                        'shop_name' => $data->added_by == 'admin' ? '' : $data->user->shop->name,
                        'shop_logo' => $data->added_by == 'admin' ? '' : $data->user->shop->logo,
                        'shop_link' => $data->added_by == 'admin' ? '' : route('shops.info', $data->user->shop->id)
                    ],
                    'category' => [
                        'name' => $data->category->name,
                        'banner' => $data->category->banner,
                        'icon' => $data->category->icon,
                        'category_links' => [
                            'products' => route('api.products.category', $data->category_id),
                            'sub_categories' => route('subCategories.index', $data->category_id)
                        ]
                    ],
                    'sub_category' => [
                        'name' => $data->subCategory != null ? $data->subCategory->name : null,
                        'sub_category_links' => [
                            'products' => $data->subCategory != null ? route('products.subCategory', $data->subcategory_id) : null
                        ]
                    ],
                    'brand' => [
                        'name' => $data->brand != null ? $data->brand->name : null,
                        'logo' => $data->brand != null ? $data->brand->logo : null,
                        'brand_links' => [
                            'products' => $data->brand != null ? route('api.products.brand', $data->brand_id) : null
                        ]
                    ],
                    'photos' => json_decode($data->photos),
                    'thumbnail_image' => $data->thumbnail_img,
                    'tags' => explode(',', $data->tags),
                    'price_lower' => (double) explode('-', homeDiscountedPrice($data->id))[0],
                    'price_higher' => (double) explode('-', homeDiscountedPrice($data->id))[1],
                    'choice_options' => $this->convertToChoiceOptions(json_decode($data->choice_options)),
                    'colors' => json_decode($data->colors),
                    'todays_deal' => (integer) $data->todays_deal,
                    'featured' => (integer) $data->featured,
                    'current_stock' => (integer) $data->current_stock,
                    'unit' => $data->unit,
                    'discount' => (double) $data->discount,
                    'discount_type' => $data->discount_type,
                    'tax' => (double) $data->tax,
                    'tax_type' => $data->tax_type,
                    'shipping_type' => $data->shipping_type,
                    'shipping_cost' => (double) $data->shipping_cost,
                    'number_of_sales' => (integer) $data->num_of_sale,
                    'rating' => (double) $data->rating,
                    'rating_count' => (integer) Review::where(['product_id' => $data->id])->count(),
                    'description' => $data->description,
                    'facebookLink' => "https://www.facebook.com/lifeokshopsivihs",
                    'messengerLink' => "https://www.facebook.com/messages/t/274924232873026",
                    'deliverText' => "7 days delivery",
                    'links' => [
                        'reviews' => route('api.reviews.index', $data->id),
                        'related' => route('products.related', $data->id)
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

    protected function convertToChoiceOptions($data){
        $result = array();
        foreach ($data as $key => $choice) {
            $item['name'] = $choice->attribute_id;
            $item['title'] = Attribute::find($choice->attribute_id)->name;
            $item['options'] = $choice->values;
            array_push($result, $item);
        }
        return $result;
    }
}
