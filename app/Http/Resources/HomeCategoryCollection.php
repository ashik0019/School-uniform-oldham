<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\ResourceCollection;

class HomeCategoryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    'id' => $data->category->id,
                    'name' => $data->category->name,
                    'banner' => $data->category->banner,
                    'icon' => $data->category->icon,
                    'products' =>  new ProductCollection(Product::where('category_id', $data->category->id)->orderBy('num_of_sale', 'desc')->paginate(10)),
                    'links' => [
                        'products' => route('api.products.category', $data->category->id),
                        'sub_categories' => route('subCategories.index', $data->category->id)
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

