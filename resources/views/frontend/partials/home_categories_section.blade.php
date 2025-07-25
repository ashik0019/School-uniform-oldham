@foreach (\App\HomeCategory::where('status', 1)->get() as $key => $homeCategory)
    @if ($homeCategory->category != null)
        <section class="mb-4">
            <div class="container">
                <div class="py-2">
                    <div class="section-title-1 clearfix">
                        <h3 class="heading-5 strong-700 mb-0 float-lg-left">
                            <span class="mr-4">{{ translate($homeCategory->category->name) }}</span>
                        </h3>
                        <ul class="inline-links float-lg-right nav mt-3 mb-2 m-lg-0">
                            <li><a href="{{ route('products.category', $homeCategory->category->slug) }}" class="active">View More</a></li>
                        </ul>
                    </div>
                    <div class="caorusel-box arrow-round gutters-5">
                        <div class="slick-carousel" data-slick-items="6" data-slick-xl-items="5" data-slick-lg-items="4"  data-slick-md-items="3" data-slick-sm-items="2" data-slick-xs-items="2">
                        @foreach (filter_products(\App\Product::where('published', 1)->where('category_id', $homeCategory->category->id))->latest()->limit(12)->get() as $key => $product)
                            <div class="caorusel-card">
                                <div class="product-box-2 bg-white alt-box my-2">
                                    <div class="position-relative overflow-hidden">
                                        @if ($product->discount > 0)
                                            @if($product->discount_type == 'amount')
                                               <div class="badge-offer">{{format_price(convert_price($product->discount))}} off</div>
                                            @else
                                                <div class="badge-offer">{{$product->discount}}% off</div>
                                            @endif
                                        @endif
                                        <a href="{{ route('product', $product->slug) }}" class="d-block product-image h-100 text-center">
                                            <img class="img-fit lazyload" src="{{ static_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($product->thumbnail_img) }}" alt="{{ __($product->name) }}">
                                        </a>
                                        @if( $product->variant_product == 0 && $product->current_stock <= 0)
                                            <div class="text-center pt-0 pr-0" style="transform: rotate(12deg);position: absolute;left: 50px;top: 93px;">
                                                <ul class="inline-links inline-links--style-1">
                                                    <li>
                                                        <span class="c-red font-weight-bold" style="border:1px solid red;border-radius:5px;font-size: 30px;">SOLD OUT</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif

                                        <div class="product-btns clearfix">
                                            <button class="btn add-wishlist" title="Add to Wishlist" onclick="addToWishList({{ $product->id }})" tabindex="0">
                                                <i class="la la-heart-o"></i>
                                            </button>
                                            <button class="btn add-compare" title="Add to Compare" onclick="addToCompare({{ $product->id }})" tabindex="0">
                                                <i class="la la-refresh"></i>
                                            </button>
                                            <button class="btn quick-view" title="Quick view" onclick="showAddToCartModal({{ $product->id }})" tabindex="0">
                                                <i class="la la-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="p-md-3 p-2">
                                        <h2 class="product-title p-0">
                                            <a href="{{ route('product', $product->slug) }}" class=" text-truncate">{{ __($product->name) }}</a>
                                        </h2>
                                        <div class="price-box">
                                            @if(home_base_price($product->id) != home_discounted_base_price($product->id))
                                                <del class="old-product-price strong-400">{{ home_base_price($product->id) }}</del>
                                            @endif
                                            <span class="product-price strong-600">{{ home_discounted_base_price($product->id) }}</span>
                                        </div>
                                        <div class="star-rating star-rating-sm mt-1">
                                            {{ renderStarRating($product->rating) }}
                                        </div>

                                        <div class="overflow-hidden">
                                            <a href="{{ route('product', $product->slug) }}" class="d-block">
                                            </a>
                                            <div class="product-btns clearfix">
                                                <button class="atcbtn" title="AddToCart" onclick="showAddToCartModal({{ $product->id }})" tabindex="0">
                                                    Add To Cart
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endforeach
