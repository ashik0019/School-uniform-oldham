<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\SearchController;
use App\Http\Resources\BrandCollection;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\ColorCollection;
use App\Http\Resources\FilterProductCollection;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductDetailCollection;
use App\Http\Resources\SearchProductCollection;
use App\Http\Resources\FlashDealCollection;
use App\Http\Resources\SubCategoryCollection;
use App\Http\Resources\SubSubCategoryCollection;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Color;
use App\Models\Seller;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Svg\Tag\Rect;

class ProductController extends Controller
{
    use ResponseAPI;

    public function index()
    {
        try {
            $res =  new ProductCollection(Product::latest()->paginate(10));
            return $this->success("Successfully fetched products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function show($id)
    {
        try {
            $res =  new ProductDetailCollection(Product::where('id', $id)->get());
            return $this->success("Successfully fetched product details", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function admin()
    {
        try {
            $res =  new ProductDetailCollection(Product::where('added_by', 'admin')->latest()->paginate(10));
            return $this->success("Successfully fetched admin products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function seller()
    {
        try {
            $res =  new ProductCollection(Product::where('added_by', 'seller')->latest()->paginate(10));
            return $this->success("Successfully fetched seller products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function category($id)
    {
        try {
            $res =  new ProductCollection(Product::where('category_id', $id)->latest()->paginate(10));
            return $this->success("Successfully fetched category products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function subCategory($id)
    {
        try {
            $res =  new ProductCollection(Product::where('subcategory_id', $id)->latest()->paginate(10));
            return $this->success("Successfully fetched subcategory products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function subSubCategory($id)
    {
        try {
            $res =  new ProductCollection(Product::where('subsubcategory_id', $id)->latest()->paginate(10));
            return $this->success("Successfully fetched sub child category products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function brand($id)
    {
        try {
            $res =  new ProductCollection(Product::where('brand_id', $id)->latest()->paginate(10));
            return $this->success("Successfully fetched brand products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function todaysDeal()
    {
        try {
            $res =  new ProductCollection(Product::where('todays_deal', 1)->latest()->get());
            return $this->success("Successfully fetched todays_deal products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function flashDeal()
    {
        try {
            $flash_deals = FlashDeal::where('status', 1)->where('featured', 1)->where('start_date', '<=', strtotime(date('d-m-Y')))->where('end_date', '>=', strtotime(date('d-m-Y')))->get();

            if ($flash_deals->count() > 0) {
                $res =  new FlashDealCollection($flash_deals);
                return $this->success("Successfully fetched flash deals products", $res);
            } else {
                $res = [];
                return $this->success("Successfully fetched flash deals products", $res);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function featured()
    {
        try {
            $res =  new ProductCollection(Product::where('featured', 1)->latest()->get());
            return $this->success("Successfully fetched featured products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function bestSeller()
    {
        try {
            $res =  new ProductCollection(Product::orderBy('num_of_sale', 'desc')->limit(20)->get());
            return $this->success("Successfully fetched best sales products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function related($id)
    {
        try {
            $product = Product::find($id);
            $res = new ProductCollection(Product::where('subcategory_id', $product->subcategory_id)->where('id', '!=', $id)->limit(10)->get());
            return $this->success("Successfully fetched related products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
    public function suggestedProducts()
    {
        try {
            $res = new ProductCollection(Product::inRandomOrder()->limit(10)->get());
            return $this->success("Successfully fetched suggested products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function topFromSeller($id)
    {
        try {
            $product = Product::find($id);
            $res = new ProductCollection(Product::where('user_id', $product->user_id)->orderBy('num_of_sale', 'desc')->limit(4)->get());
            return $this->success("Successfully fetched topFromSeller products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function search(Request $request)
    {
        $search = $request->q;
        try {
            $keywords = array();
            $subsubcategories = array();

            if ($search) {
                $products = Product::where('published', 1)->where('tags', 'like', '%' . $search . '%')->get();
                foreach ($products as $key => $product) {
                    foreach (explode(',', $product->tags) as $key => $tag) {
                        if (stripos($tag, $search) !== false) {
                            if (sizeof($keywords) > 5) {
                                break;
                            } else {
                                if (!in_array(strtolower($tag), $keywords)) {
                                    array_push($keywords, strtolower($tag));
                                }
                            }
                        }
                    }
                }

                $products = filter_products(Product::where('published', 1)->where('name', 'like', '%' . $search . '%'))->get()->take(5);
                $products = new ProductCollection($products);
                $subsubcategories = Category::where('name', 'like', '%' . $search . '%')->get()->take(3);
            } else {
                $products = new ProductCollection(Product::inRandomOrder()->limit(5)->get());
            }

            $res = [
                'categories' => $subsubcategories,
                'products' => $products,
            ];

            return $this->success("Successfully fetched search products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
    public function search2()
    {
        try {
            $key = request('key');
            $scope = request('scope');

            switch ($scope) {

                case 'price_low_to_high':
                    $collection = new SearchProductCollection(Product::where('name', 'like', "%{$key}%")->orWhere('tags', 'like', "%{$key}%")->orderBy('unit_price', 'asc')->paginate(10));
                    $collection->appends(['key' =>  $key, 'scope' => $scope]);
                    return $this->success("Successfully fetched price_low_to_high wise products", $collection);

                case 'price_high_to_low':
                    $collection = new SearchProductCollection(Product::where('name', 'like', "%{$key}%")->orWhere('tags', 'like', "%{$key}%")->orderBy('unit_price', 'desc')->paginate(10));
                    $collection->appends(['key' =>  $key, 'scope' => $scope]);
                    return $this->success("Successfully fetched price_high_to_low wise products", $collection);

                case 'new_arrival':
                    $collection = new SearchProductCollection(Product::where('name', 'like', "%{$key}%")->orWhere('tags', 'like', "%{$key}%")->orderBy('created_at', 'desc')->paginate(10));
                    $collection->appends(['key' =>  $key, 'scope' => $scope]);
                    return $this->success("Successfully fetched new_arrival wise products", $collection);

                case 'popularity':
                    $collection = new SearchProductCollection(Product::where('name', 'like', "%{$key}%")->orWhere('tags', 'like', "%{$key}%")->orderBy('num_of_sale', 'desc')->paginate(10));
                    $collection->appends(['key' =>  $key, 'scope' => $scope]);
                    return $this->success("Successfully fetched popularity wise products", $collection);

                case 'top_rated':
                    $collection = new SearchProductCollection(Product::where('name', 'like', "%{$key}%")->orWhere('tags', 'like', "%{$key}%")->orderBy('rating', 'desc')->paginate(10));
                    $collection->appends(['key' =>  $key, 'scope' => $scope]);
                    return $this->success("Successfully fetched top_rated wise products", $collection);

                case 'category':
                    $categories = Category::select('id')->where('name', 'like', "{$key}")->get()->toArray();
                    $collection = new SearchProductCollection(Product::where('category_id', $categories)->orderBy('num_of_sale', 'desc')->paginate(10));
                    $collection->appends(['key' =>  $key, 'scope' => $scope]);
                    return $this->success("Successfully fetched category wise products", $collection);

                case 'subcategory':
                    $subcategories = SubCategory::select('id')->where('name', 'like', "{$key}")->get()->toArray();
                    //dd($subcategories);
                    $collection = new SearchProductCollection(Product::where('subcategory_id', $subcategories)->orderBy('num_of_sale', 'desc')->paginate(10));
                    $collection->appends(['key' =>  $key, 'scope' => $scope]);
                    return $this->success("Successfully fetched subcategori wise products", $collection);

                case 'brand':

                    $brands = Brand::select('id')->where('name', 'like', "%{$key}%")->get()->toArray();
                    $collection = new SearchProductCollection(Product::where('brand_id', $brands)->orderBy('num_of_sale', 'desc')->paginate(10));
                    $collection->appends(['key' =>  $key, 'scope' => $scope]);
                    return $this->success("Successfully fetched brand wise products", $collection);

                case 'shop':

                    $shops = Shop::select('user_id')->where('name', 'like', "%{$key}%")->get()->toArray();
                    $collection = new SearchProductCollection(Product::where('user_id', $shops)->orderBy('num_of_sale', 'desc')->paginate(10));
                    $collection->appends(['key' =>  $key, 'scope' => $scope]);
                    return $this->success("Successfully fetched products", $collection);

                default:
                    $collection = new SearchProductCollection(Product::where('name', 'like', "%{$key}%")->orWhere('tags', 'like', "%{$key}%")->orderBy('num_of_sale', 'desc')->paginate(10));
                    $collection->appends(['key' =>  $key, 'scope' => $scope]);
                    return $this->success("Successfully fetched products", $collection);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function variantPrice(Request $request)
    {
        try {
            $product = Product::findOrFail($request->id);
            $str = '';
            $tax = 0;

            if ($request->has('color')) {
                //dd($request->color);
                $data['color'] = $request['color'];
                $str = Color::where('code', $request['color'])->first()->name;
            }

            foreach (json_decode($request->choice) as $option) {

                $str .= $str != '' ?  '-' . str_replace(' ', '', $option->name) : str_replace(' ', '', $option->name);
            }
            //dd($str);

            if ($str != null && $product->variant_product) {
                $product_stock = $product->stocks->where('variant', $str)->first();
                $price = $product_stock->price;
                $stockQuantity = $product_stock->qty;
            } else {
                $price = $product->unit_price;
                $stockQuantity = $product->current_stock;
            }

            //discount calculation
            $flash_deals = FlashDeal::where('status', 1)->get();
            $inFlashDeal = false;
            foreach ($flash_deals as $key => $flash_deal) {
                if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first() != null) {
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
                $price += ($price * $product->tax) / 100;
            } elseif ($product->tax_type == 'amount') {
                $price += $product->tax;
            }

            $res = [
                'product_id' => $product->id,
                'variant' => $str,
                'price' => (float) $price,
                'in_stock' => $stockQuantity < 1 ? false : true,
                'in_stock_qty' => $stockQuantity
            ];
            return $this->success("Successfully fetched product variant price", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function home()
    {
        try {
            $res = new ProductCollection(Product::inRandomOrder()->take(50)->get());
            return $this->success("Successfully fetched home products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getFilter(Request $request)
    {
        try {
            $filetercategory =  new CategoryCollection(Category::latest()->get());
            $brands = new BrandCollection(Brand::all());
            $filtersubcategories = new SubCategoryCollection(SubCategory::where('category_id', $request->category)->get());;
            $query = $request->q;
            $brand_id = (Brand::where('id', $request->brand)->first() != null) ? Brand::where('id', $request->brand)->first()->id : null;
            $category_id = (Category::where('id', $request->category)->first() != null) ? Category::where('id', $request->category)->first()->id : null;
            $subcategory_id = (SubCategory::where('id', $request->subcategory)->first() != null) ? SubCategory::where('id', $request->subcategory)->first()->id : null;
            $subsubcategory_id = (SubSubCategory::where('id', $request->subsubcategory)->first() != null) ? SubSubCategory::where('id', $request->subsubcategory)->first()->id : null;
            $conditions = ['published' => 1];

            if ($brand_id != null) {
                $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
            }
            if ($category_id != null) {
                $conditions = array_merge($conditions, ['category_id' => $category_id]);
            }
            if ($subcategory_id != null) {
                $conditions = array_merge($conditions, ['subcategory_id' => $subcategory_id]);
            }
            if ($subsubcategory_id != null) {
                $conditions = array_merge($conditions, ['subsubcategory_id' => $subsubcategory_id]);
            }

            $products = Product::where($conditions);
            $non_paginate_products = filter_products($products)->get();

            //Attribute Filter
            $attributes = array();
            foreach ($non_paginate_products as $key => $product) {
                if ($product->attributes != null && is_array(json_decode($product->attributes))) {
                    foreach (json_decode($product->attributes) as $key => $value) {
                        $flag = false;
                        $pos = 0;
                        foreach ($attributes as $key => $attribute) {
                            if ($attribute['id'] == $value) {
                                $flag = true;
                                $pos = $key;
                                break;
                            }
                        }
                        if (!$flag) {
                            $item['id'] = $value;
                            $item['name'] = Attribute::find($value)->name ?? 'Unknown'; // Fetch the name
                            $item['values'] = array();
                            foreach (json_decode($product->choice_options) as $key => $choice_option) {
                                if ($choice_option->attribute_id == $value) {
                                    $item['values'] = $choice_option->values;
                                    break;
                                }
                            }
                            array_push($attributes, $item);
                        } else {
                            foreach (json_decode($product->choice_options) as $key => $choice_option) {
                                if ($choice_option->attribute_id == $value) {
                                    foreach ($choice_option->values as $key => $value) {
                                        if (!in_array($value, $attributes[$pos]['values'])) {
                                            array_push($attributes[$pos]['values'], $value);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $selected_attributes = array();

            foreach ($attributes as $key => $attribute) {
                if ($request->has('attribute_' . $attribute['id'])) {
                    foreach ($request['attribute_' . $attribute['id']] as $key => $value) {
                        $str = '"' . $value . '"';
                        $products = $products->where('choice_options', 'like', '%' . $str . '%');
                    }

                    $item['id'] = $attribute['id'];
                    $item['values'] = $request['attribute_' . $attribute['id']];
                    array_push($selected_attributes, $item);
                }
            }


            //Color Filter
            $all_colors = array();

            foreach ($non_paginate_products as $key => $product) {
                if ($product->colors != null) {
                    foreach (json_decode($product->colors) as $key => $color) {
                        if (!in_array($color, $all_colors)) {
                            array_push($all_colors, $color);
                        }
                    }
                }
            }

            $res = [
                'categories' => $filetercategory,
                'filtersubcategories' => $filtersubcategories,
                'brands' => $brands,
                'colors' => $all_colors,
                'attributes' => $attributes,
            ];

            return $this->success("Successfully fetched Filter Data", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
    public function filteredProducts(Request $request)
    {
        try {
            
            $query = $request->q;
            $todayDeal = $request->todays_deal;
            $newArrival = $request->new_arrival;
            $topRated = $request->top_rated;
            $bestSelling = $request->best_selling;
            $brand_id = (Brand::where('id', $request->brand)->first() != null) ? Brand::where('id', $request->brand)->first()->id : null;
            $sort_by = $request->sort_by;
            $category_id = (Category::where('id', $request->category)->first() != null) ? Category::where('id', $request->category)->first()->id : null;
            $subcategory_id = (SubCategory::where('id', $request->subcategory)->first() != null) ? SubCategory::where('id', $request->subcategory)->first()->id : null;
            $subsubcategory_id = (SubSubCategory::where('id', $request->subsubcategory)->first() != null) ? SubSubCategory::where('id', $request->subsubcategory)->first()->id : null;
            $min_price = $request->min_price;
            $max_price = $request->max_price;
            $seller_id = $request->seller_id;

            $conditions = ['published' => 1];

            if ($brand_id != null) {
                $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
            }
            if ($category_id != null) {
                $conditions = array_merge($conditions, ['category_id' => $category_id]);
            }
            if ($subcategory_id != null) {
                $conditions = array_merge($conditions, ['subcategory_id' => $subcategory_id]);
            }
            if ($subsubcategory_id != null) {
                $conditions = array_merge($conditions, ['subsubcategory_id' => $subsubcategory_id]);
            }

            if ($seller_id != null) {
                $conditions = array_merge($conditions, ['user_id' => Seller::findOrFail($seller_id)->user->id]);
            }
            if ($todayDeal != null) {
                //$conditions['todays_deal'] = 1;
                $conditions = array_merge($conditions, ['todays_deal' => 1]);
            }
            if ($newArrival != null) {
                //$conditions['todays_deal'] = 1;
                $conditions = array_merge($conditions, ['featured' => 1]);
            }

            $products = Product::where($conditions);

            if ($bestSelling != null) {
                $products = $products->orderBy('num_of_sale', 'desc');
            }

            if ($topRated != null) {
                $products = $products->orderBy('rating', 'desc');
            }


            if ($min_price != null && $max_price != null) {
                $products = $products->where('unit_price', '>=', $min_price)->where('unit_price', '<=', $max_price);
            }

            if ($query != null) {
                $searchController = new SearchController;
                $searchController->store($request);
                $products = $products->where('name', 'like', '%' . $query . '%')->orWhere('tags', 'like', '%' . $query . '%');
            }

            if ($sort_by != null) {
                switch ($sort_by) {
                    case '1':
                        $products->orderBy('created_at', 'desc');
                        break;
                    case '2':
                        $products->orderBy('created_at', 'asc');
                        break;
                    case '3':
                        $products->orderBy('unit_price', 'asc');
                        break;
                    case '4':
                        $products->orderBy('unit_price', 'desc');
                        break;
                    default:
                        // code...
                        break;
                }
            }


            $non_paginate_products = filter_products($products)->get();

            //Attribute Filter

            $attributes = array();
            foreach ($non_paginate_products as $key => $product) {
                if ($product->attributes != null && is_array(json_decode($product->attributes))) {
                    foreach (json_decode($product->attributes) as $key => $value) {
                        $flag = false;
                        $pos = 0;
                        foreach ($attributes as $key => $attribute) {
                            if ($attribute['id'] == $value) {
                                $flag = true;
                                $pos = $key;
                                break;
                            }
                        }
                        if (!$flag) {
                            $item['id'] = $value;
                            $item['values'] = array();
                            foreach (json_decode($product->choice_options) as $key => $choice_option) {
                                if ($choice_option->attribute_id == $value) {
                                    $item['values'] = $choice_option->values;
                                    break;
                                }
                            }
                            array_push($attributes, $item);
                        } else {
                            foreach (json_decode($product->choice_options) as $key => $choice_option) {
                                if ($choice_option->attribute_id == $value) {
                                    foreach ($choice_option->values as $key => $value) {
                                        if (!in_array($value, $attributes[$pos]['values'])) {
                                            array_push($attributes[$pos]['values'], $value);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $selected_attributes = array();

            foreach ($attributes as $key => $attribute) {
                if ($request->has('attribute_' . $attribute['id'])) {
                    foreach ($request['attribute_' . $attribute['id']] as $key => $value) {
                        $str = '"' . $value . '"';
                        $products = $products->where('choice_options', 'like', '%' . $str . '%');
                    }

                    $item['id'] = $attribute['id'];
                    $item['values'] = $request['attribute_' . $attribute['id']];
                    array_push($selected_attributes, $item);
                }
            }


            //Color Filter
            $all_colors = array();

            foreach ($non_paginate_products as $key => $product) {
                if ($product->colors != null) {
                    foreach (json_decode($product->colors) as $key => $color) {
                        if (!in_array($color, $all_colors)) {
                            array_push($all_colors, $color);
                        }
                    }
                }
            }

            $selected_color = null;

            if ($request->has('color')) {
                $str = '"' . $request->color . '"';
                $products = $products->where('colors', 'like', '%' . $str . '%');
                $selected_color = $request->color;
            }
            //$products = filter_products($products)->paginate(12)->appends(request()->query());
            // $products = filter_products($products)->paginate(12)->appends(request()->query());

            if ($request->flash_deal) {
                // Fetch the active flash deal
                $flash_deal = FlashDeal::where('status', 1)
                    ->where('featured', 1)
                    ->where('start_date', '<=', strtotime(date('d-m-Y')))
                    ->where('end_date', '>=', strtotime(date('d-m-Y')))
                    ->first();

                if ($flash_deal) {
                    // Fetch paginated products for the flash deal
                    $products = Product::join('flash_deal_products', 'products.id', '=', 'flash_deal_products.product_id')
                        ->where('flash_deal_products.flash_deal_id', $flash_deal->id)
                        ->select(
                            'products.*',
                            // 'products.name',
                            // 'products.price',
                            // 'flash_deal_products.discount',
                            // 'flash_deal_products.discount_type'
                        )
                        ->paginate(10);
                }
            } else {
                $products = filter_products($products)->paginate(12)->appends(request()->query());
            }
            $products = new FilterProductCollection($products);
            $res = [
                // 'filetercategory' => $filetercategory,
                // 'filtersubcategory' => $filtersubcategory,
                // 'filterinnercategory' => $filterinnercategory,
                // 'brands' => $brands,
                // 'min_price' => $min_price,
                // 'max_price' => $max_price,
                // 'sort_by' => $sort_by,
                // 'colors' => $all_colors,
                // 'attributes' => $attributes,
                'products' => $products,
            ];

            return $this->success("Successfully fetched Filter Data", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function queryProducts(Request $request)
    {
        try {
            $queryKey = $request->key;
            $queryValue = $request->value;
            $conditions = ['published' => 1]; // Default condition

            // Apply filter conditions based on query key and value
            if ($queryKey == 'brand') {
                $conditions['brand_id'] = $queryValue;
            } elseif ($queryKey == 'category') {
                $conditions['category_id'] = $queryValue;
            } elseif ($queryKey == 'subcategory') {
                $conditions['subcategory_id'] = $queryValue;
            } elseif ($queryKey == 'subsubcategory') {
                $conditions['subsubcategory_id'] = $queryValue;
            } elseif ($queryKey == 'todays_deal') {
                $conditions['todays_deal'] = 1;
            } elseif ($queryKey == 'new-arrival') {
                $conditions['featured'] = 1;
            }

            // Start the query with the conditions applied
            $products = Product::where($conditions);

            // Apply sorting based on query key
            if ($queryKey == 'best-selling') {
                $products = $products->orderBy('num_of_sale', 'desc');
            } elseif ($queryKey == 'top-rated') {
                $products = $products->orderBy('rating', 'desc');
            }

            // Further filter and paginate the products, then convert to collection
            $products = filter_products($products)->paginate(20)->appends(request()->query());
            $products = new FilterProductCollection($products);

            // Prepare response data
            $res = [
                'key' => $queryKey,
                'value' => $queryValue,
                'products' => $products,
            ];

            return $this->success("Successfully fetched Query Filter products", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getAllHomeProducts(Request $request)
    {
        try {
            $conditions = ['published' => 1]; // Default condition
            $products = Product::where($conditions)->latest();
            // Further filter and paginate the products, then convert to collection
            $products = filter_products($products)->paginate(20)->appends(request()->query());
            $products = new FilterProductCollection($products);
            // $res = [
            //     'products' => $products,
            // ];
            return $this->success("Successfully fetched Query Filter products", $products);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
