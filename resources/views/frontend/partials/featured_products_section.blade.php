<section class="mb-4">
    <div class="container">
        <div class="py-2">
            <div class="section-title-1 clearfix">
                <h3 class="heading-5 strong-700 mb-0 float-left">
                    <span class="mr-4">{{ translate('New Arrival')}}</span>
                </h3>
            </div>
            <div class="caorusel-box arrow-round gutters-5">
                <div class="slick-carousel" data-slick-items="6" data-slick-xl-items="5" data-slick-lg-items="4"
                     data-slick-md-items="3" data-slick-sm-items="2" data-slick-xs-items="2">
                    @foreach (filter_products(\App\Product::where('published', 1)->where('featured', '1'))->limit(20)->latest()->get() as $key => $product)
                        <div class="caorusel-card">
                            <div class="product-card-2 card card-product shop-cards shop-tech">
                                <div class="card-body p-0">

                                    <div class="card-image">
                                        @if ($product->discount > 0)
                                            @if($product->discount_type == 'amount')
                                                <div class="badge-offer">{{format_price(convert_price($product->discount))}} off</div>
                                            @else
                                                <div class="badge-offer">{{$product->discount}}% off</div>
                                            @endif
                                        @endif
                                        <a href="{{ route('product', $product->slug) }}" class="d-block">
                                            <img class="img-fit lazyload mx-auto"
                                                 src="{{ static_asset('frontend/images/placeholder.jpg') }}"
                                                 data-src="{{ my_asset($product->thumbnail_img) }}"
                                                 alt="{{ __($product->name) }}">

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
                                    </div>

                                    <div class="p-md-3 p-2">
                                        <h2 class="product-title p-0">
                                            <a href="{{ route('product', $product->slug) }}"
                                               class="text-truncate">{{ __($product->name) }}</a>
                                        </h2>
                                        <div class="price-box">
                                            @if(home_base_price($product->id) != home_discounted_base_price($product->id))
                                                <del
                                                    class="old-product-price strong-400">{{ home_base_price($product->id) }}</del>
                                            @endif
                                            <span
                                                class="product-price strong-600">{{ home_discounted_base_price($product->id) }}</span>
                                        </div>
                                        <div class="star-rating star-rating-sm mt-1">
                                            {{ renderStarRating($product->rating) }}
                                        </div>
                                        <div class="overflow-hidden">
                                            <a href="{{ route('product', $product->slug) }}" class="d-block">
                                            </a>
                                            <div class="product-btns clearfix">
                                                <button class="atcbtn" title="AddToCart"
                                                        onclick="showAddToCartModal({{ $product->id }})" tabindex="0">
                                                    Add To Cart
                                                </button>

                                            </div>
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
