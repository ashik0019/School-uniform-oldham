@extends('frontend.layouts.app')

@section('content')
    <section class="home-banner-area mb-4" style="background: #f2f2f2;">
        <div class="container">
            <div class="row no-gutters position-relative">
                <div class="col-lg-3 position-static order-2 order-lg-0">
                    <div class="category-sidebar mt-2 mr-2" style="border-radius: 5px; height: 98%">
                        <a href="{{ route('categories.all') }}">
                            <div class="all-category d-none d-lg-block">
                                <i class="fa fa-bars mr-1"> </i><span class="d-none d-lg-inline-block font-weight-bold"> {{ translate('Categories') }}</span>
                                <button class="btn btn-success float-right mt-2 mr-1">View All</button>
<!--                                   <span class="d-none d-lg-inline-block btn btn-success float-right">{{ translate('See All') }} ></span>-->
                            </div>
                        </a>
                        <ul class="categories no-scrollbar">
                            <li class="d-lg-none">
                                <a href="{{ route('categories.all') }}" class="text-truncate">
                                    <img class="cat-image lazyload" src="{{ static_asset('frontend/images/placeholder.jpg') }}" data-src="{{ static_asset('frontend/images/icons/list.png') }}" width="30" alt="{{ translate('All Category') }}">
                                    <span class="cat-name">{{ translate('All') }} <br> {{ translate('Categories') }}</span>
                                </a>
                            </li>
                             @foreach (\App\Category::where('name', '!=', 'Universal')->take(8)->get() as $key => $category)
                                @php
                                   $brands = array();
                                @endphp
                                <li class="category-nav-element" data-id="{{ $category->id }}">
                                    <a href="{{ route('products.category', $category->slug) }}" class="text-truncate">
                                        <img class="cat-image lazyload" src="{{ static_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($category->icon) }}" width="30" alt="{{ __($category->name) }}">
                                        <span class="cat-name">{{ __($category->name) }}</span>
                                    </a>
                                    @if(count($category->subcategories)>0)
                                        <div class="sub-cat-menu c-scrollbar">
                                            <div class="c-preloader">
                                                <i class="fa fa-spin fa-spinner"></i>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                @php
                    $num_todays_deal = count(filter_products(\App\Product::where('published', 1)->where('todays_deal', 1 ))->get());
                    $featured_categories = \App\Category::where('featured', 1)->get();
                @endphp

                <div class="@if($num_todays_deal > 10000000)  col-lg-7 @else col-lg-9 @endif order-1 order-lg-0 @if(count($featured_categories) == 0) home-slider-full @endif">
                    <div class="home-slide mt-2 pl-1 pb-2">
                        <div class="home-slide">
                            <div class="slick-carousel" data-slick-arrows="true" data-slick-dots="true" data-slick-autoplay="true">
                                @foreach (\App\Slider::where('published', 1)->get() as $key => $slider)
                                    <div class="home-slide-item" style="height:275px;">
                                        <a href="{{ $slider->link }}" target="_blank">
                                        <img style="border-radius: 7px;" class="d-block w-100 h-100 lazyload" src="{{ static_asset('frontend/images/placeholder-rect.jpg') }}" data-src="{{ my_asset($slider->photo) }}" alt="{{ env('APP_NAME')}} promo">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @if (count($featured_categories) > 0)
                        <div class="trending-category  d-none d-lg-block pl-1" >
                            <ul style="border-radius: 5px;">
                                @foreach ($featured_categories->take(7) as $key => $category)
                                    <li @if ($key == 0) class="active" @endif>
                                        <div class="trend-category-single">
                                            <a href="{{ route('products.category', $category->slug) }}" class="d-block">
                                                <div class="name">{{ __($category->name) }}</div>
                                                <div class="img">
                                                    <img src="{{ static_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($category->banner) }}" alt="{{ __($category->name) }}" class="lazyload img-fit">
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                @if($num_todays_deal > 100000000)
                <div class="col-lg-2 d-none d-lg-block  mt-3">
                    <div class="flash-deal-box bg-white h-100 ml-3 ">
                        <div class="title text-center p-2 gry-bg">
                            <h3 class="heading-6 mb-0">
<!--                                {{ translate('Todays Deal') }}-->
<!--                                <span class="badge badge-danger">{{ translate('Hot') }}</span>-->
<!--                                <span class="badge badge-danger">
                                    <img src="{{asset('img/home_avatar.png')}}" alt="">
                                </span>-->
                            </h3>
                        </div>
                        <div class="flash-content c-scrollbar c-height text-center">
                            @if (Auth::check())
                                <a href="{{route('dashboard')}}">
                                    @if (Auth::user()->avatar_original != null)
                                        <img class="home-avater rounded-circle" src="{{my_asset(Auth::user()->avatar_original)}}" alt="">
                                    @else
                                        <img class="home-avater rounded-circle" src="{{asset('public/img/home_avatar.png')}}" alt="">
                                    @endif
                                </a>
                            @else
                                <img class="home-avater" src="{{asset('public/img/home_avatar.png')}}" alt="">
                            @endif
                                <p class="pt-5 strong-700 text-white"><strong>Welcome to Life Ok</strong></p>
                            <div class="log-reg-btn">
                                <span class="w-50"><a href="{{route('user.registration')}}" class="btn btn-danger" >Join</a></span>
                                <span><a href="{{route('user.registration')}}" class="btn btn-secondary" >Sign In</a></span>
                            </div>
                            <div class="mt-4 custome-border">
                                <p class="p-2 text-left text-white">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium consequatur in nisi quas quos. Ab aperiam atque consequatur culpa dolore ,</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </section>
{{--  Category page  --}}
   {{-- <section class="mb-4">
        <div class="container">
            <div class="py-2">
                <div class="section-title-1 clearfix ">
                    <h3 class="heading-5 strong-700 mb-0 float-left">
                        {{ translate('Imported Product Categories') }}
                    </h3>
                </div>
                <div class="caorusel-box arrow-round gutters-5 row">
                    @foreach (\App\Banner::where('position', 1)->where('published', 1)->get() as $key => $banner)
                    <!--                    <div class="col-lg-{{ 12/count(\App\Banner::where('position', 1)->where('published', 1)->get()) }}">-->
                        <div class="col-lg-4 col-md-3 mb-2">
                            <div class="media-banner mb-3 mb-lg-0">
<!--                                <a href="{{ $banner->url }}" target="_blank" class="banner-container">
                                    <img src="{{ static_asset('frontend/images/placeholder-rect.jpg') }}" data-src="{{ my_asset($banner->photo) }}" alt="{{ env('APP_NAME') }} promo" class="img-fluid lazyload custome-border">
                                </a>-->
                                <ul class="image-grid">
                                    <li class="image-grid__item" title="Click here to get this category products">
                                        <a href="#" class="grid-item">
                                            <div class="grid-item__image img-fluid lazyload custome-border" style="background-image: url({{ my_asset($banner->photo) }})"></div>
                                            <div class="grid-item__hover"></div>
--}}{{--                                            <div class="grid-item__name">Bangladesh</div>--}}{{--
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>--}}
<!--    <div class="mb-4">
        <div class="container">
            <div class="row gutters-10">
            @foreach (\App\Banner::where('position', 1)->where('published', 1)->get() as $key => $banner)
                &lt;!&ndash;                    <div class="col-lg-{{ 12/count(\App\Banner::where('position', 1)->where('published', 1)->get()) }}">&ndash;&gt;
                <div class="col-lg-4 col-md-3 mb-2">
                    <div class="media-banner mb-3 mb-lg-0">
                        <a href="{{ $banner->url }}" target="_blank" class="banner-container">
                            <img src="{{ static_asset('frontend/images/placeholder-rect.jpg') }}" data-src="{{ my_asset($banner->photo) }}" alt="{{ env('APP_NAME') }} promo" class="img-fluid lazyload">
                        </a>
                    </div>
                </div>
            @endforeach
            </div>
        </div>
    </div>-->
    @php
        $flash_deal = \App\FlashDeal::where('status', 1)->where('featured', 1)->first();
    @endphp
    @if($flash_deal != null && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date)
    <section class="mb-4">
        <div class="container">
            <div class="py-2">
                <div class="section-title-1 clearfix ">
                    <h3 class="heading-5 strong-700 mb-0 float-left">
                        {{ translate('Flash Sale') }}
                    </h3>
                    <div class="flash-deal-box float-left">
                        <div class="countdown countdown--style-1 countdown--style-1-v1 " data-countdown-date="{{ date('m/d/Y', $flash_deal->end_date) }}" data-countdown-label="show"></div>
                    </div>
                    <ul class="inline-links float-right">
                        <li><a href="{{ route('flash-deal-details', $flash_deal->slug) }}" class="active">{{ translate('View More') }}</a></li>
                    </ul>
                </div>

                <div class="caorusel-box arrow-round gutters-5">
                    <div class="slick-carousel" data-slick-items="6" data-slick-xl-items="5" data-slick-lg-items="4"  data-slick-md-items="3" data-slick-sm-items="2" data-slick-xs-items="2">
                    @foreach ($flash_deal->flash_deal_products as $key => $flash_deal_product)
                        @php
                            $product = \App\Product::find($flash_deal_product->product_id);
                        @endphp
                        @if ($product != null && $product->published != 0)
                            <div class="caorusel-card">
                                <div class="product-card-2 card card-product shop-cards">
                                    <div class="card-body p-0">
                                        <div class="card-image">
                                            @if($flash_deal_product->discount_type == 'amount')
                                            <div class="badge-offer">{{$flash_deal_product->discount}}tk off</div>
                                            @else
                                                <div class="badge-offer">{{$flash_deal_product->discount}}% off</div>
                                            @endif
                                            <a href="{{ route('product', $product->slug) }}" class="d-block">
                                                <img class="img-fit lazyload mx-auto" src="{{ static_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($product->thumbnail_img) }}" alt="{{ __($product->name) }}">
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
                            </div>
                        @endif
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif



    <div id="section_featured">

    </div>

    <div id="section_best_selling">

    </div>

    <div id="section_home_categories">

    </div>

    @if(\App\BusinessSetting::where('type', 'classified_product')->first()->value == 1)
        @php
            $customer_products = \App\CustomerProduct::where('status', '1')->where('published', '1')->take(10)->get();
        @endphp
       @if (count($customer_products) > 0)
           <section class="mb-4">
               <div class="container">
                   <div class="px-2 py-4 p-md-4 bg-white shadow-sm border-radius-5">
                       <div class="section-title-1 clearfix">
                           <h3 class="heading-5 strong-700 mb-0 float-left">
                               <span class="mr-4">{{ translate('Classified Ads') }}</span>
                           </h3>
                           <ul class="inline-links float-right">
                               <li><a href="{{ route('customer.products') }}" class="active">{{ translate('View More') }}</a></li>
                           </ul>
                       </div>
                       <div class="caorusel-box arrow-round">
                           <div class="slick-carousel" data-slick-items="6" data-slick-xl-items="5" data-slick-lg-items="4"  data-slick-md-items="3" data-slick-sm-items="2" data-slick-xs-items="2">
                               @foreach ($customer_products as $key => $customer_product)
                                   <div class="product-card-2 card card-product my-2 mx-1 mx-sm-2 shop-cards shop-tech">
                                       <div class="card-body p-0">
                                           <div class="card-image">
                                               <a href="{{ route('customer.product', $customer_product->slug) }}" class="d-block">
                                                   <img class="img-fit lazyload mx-auto" src="{{ static_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($customer_product->thumbnail_img) }}" alt="{{ __($customer_product->name) }}">
                                               </a>
                                           </div>

                                           <div class="p-sm-3 p-2">
                                               <div class="price-box">
                                                   <span class="product-price strong-600">{{ single_price($customer_product->unit_price) }}</span>
                                               </div>
                                               <h2 class="product-title p-0 text-truncate-1">
                                                   <a href="{{ route('customer.product', $customer_product->slug) }}">{{ __($customer_product->name) }}</a>
                                               </h2>
                                               <div>
                                                   @if($customer_product->conditon == 'new')
                                                       <span class="product-label label-hot">{{translate('new')}}</span>
                                                   @elseif($customer_product->conditon == 'used')
                                                       <span class="product-label label-hot">{{translate('Used')}}</span>
                                                   @endif
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
   @endif
    @if(count(\App\Category::where('top', 1)->get()) != null && count(\App\Brand::where('top', 1)->get()) != null)
        <section class="mb-3 mt-5">
            <div class="container">
                <div class="row gutters-10">
                    <div class="col-lg-6">
                        <div class="section-title-1 clearfix">
                            <h3 class="heading-5 strong-700 mb-0 float-left">
                                <span class="mr-4">{{translate('Top 10 Brands')}}</span>
                            </h3>
                            <ul class="float-right inline-links">
                                <li>
                                    <a href="{{ route('brands.all') }}" class="active">{{translate('View All Brands')}}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="row gutters-5">
                            @foreach (\App\Brand::where('top', 1)->get() as $brand)
                                <div class="mb-3 col-6">
                                    <a href="{{ route('products.brand', $brand->slug) }}" class="bg-white border d-block c-base-2 box-2 icon-anim pl-2">
                                        <div class="row align-items-center no-gutters">
                                            <div class="col-3 text-center">
                                                <img src="{{ static_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($brand->logo) }}" alt="{{ __($brand->name) }}" class="img-fluid img lazyload">
                                            </div>
                                            <div class="info col-7">
                                                <div class="name text-truncate pl-3 py-4">{{ __($brand->name) }}</div>
                                            </div>
                                            <div class="col-2 text-center">
                                                <i class="la la-angle-right c-base-1"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="section-title-1 clearfix">
                            <h3 class="heading-5 strong-700 mb-0 float-left">
                                <span class="mr-4">{{translate('Top 10 Catogories')}}</span>
                            </h3>
                            <ul class="float-right inline-links">
                                <li>
                                    <a href="{{ route('categories.all') }}" class="active">{{translate('View All Catogories')}}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="row gutters-5">
                            @foreach (\App\Category::where('top', 1)->get() as $category)
                                <div class="mb-3 col-6">
                                    <a href="{{ route('products.category', $category->slug) }}" class="bg-white border d-block c-base-2 box-2 icon-anim pl-2">
                                        <div class="row align-items-center no-gutters">
                                            <div class="col-3 text-center">
                                                <img src="{{ static_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($category->banner) }}" alt="{{ __($category->name) }}" class="img-fluid img lazyload">
                                            </div>
                                            <div class="info col-7">
                                                <div class="name text-truncate pl-3 py-4">{{ __($category->name) }}</div>
                                            </div>
                                            <div class="col-2 text-center">
                                                <i class="la la-angle-right c-base-1"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <section id="" data-type="background" data-speed="10" class="pages">
        <div class="pt-5 pb-5">
            <div class="container">
                <div class="row gutters-10">
                    @foreach (\App\Banner::where('position', 2)->where('published', 1)->get() as $key => $banner)
                        <div class="col-lg-{{ 12/count(\App\Banner::where('position', 2)->where('published', 1)->get()) }}">
                            <div class="media-banner mb-3 mb-lg-0">
                                <a href="{{ $banner->url }}" target="_blank" class="banner-container">
                                    <img src="{{ static_asset('frontend/images/placeholder-rect.jpg') }}" data-src="{{ my_asset($banner->photo) }}" alt="{{ env('APP_NAME') }} promo" class="img-fluid lazyload">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @if (\App\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
    <div id="section_best_sellers">

    </div>
    @endif


    <!-- Modal -->
    <div class="modal fade" id="login" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="text-center px-35 pt-3">
                        <h1 class="heading heading-4 strong-500">
                            {{ translate('Login to your account.')}}
                        </h1>
                    </div>

                    <div class="px-5 py-3 py-lg-4">
                        <div class="">
                            <form class="form-default" role="form" action="{{ route('login') }}" method="POST">
                                @csrf
                                @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                    <span>{{  translate('Use country code before number') }}</span>
                                @endif
                                <div class="form-group">
                                    @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                        <input type="text" class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{ translate('Email Or Phone')}}" name="email" id="email">
                                    @else
                                        <input type="email" class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{  translate('Email') }}" name="email">
                                    @endif
                                </div>

                                <div class="form-group">
                                    <input type="password" class="form-control h-auto form-control-lg {{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ translate('Password')}}" name="password" id="password">
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <div class="checkbox pad-btm text-left ">
                                                <input id="demo-form-checkbox" class="magic-checkbox" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <label for="demo-form-checkbox" class="text-sm">
                                                    {{  translate('Remember Me') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 text-right">
                                        <a href="{{ route('password.request') }}" class="link link-xs link--style-3">{{ translate('Forgot password?')}}</a>
                                    </div>
                                </div>


                                <div class="text-center">
                                    <button type="submit" class="btn btn-styled btn-base-1 btn-md w-100">{{  translate('Login') }}</button>
                                </div>
                            </form>
                            @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                <div class="or or--1 mt-3 text-center">
                                    <span>or</span>
                                </div>
                                <div>
                                    @if (\App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1)
                                        <a href="{{ route('social.login', ['provider' => 'facebook']) }}" class="btn btn-styled btn-block btn-facebook btn-icon--2 btn-icon-left px-4 mb-3">
                                            <i class="icon fa fa-facebook"></i> {{ translate('Login with Facebook')}}
                                        </a>
                                    @endif
                                    @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1)
                                        <a href="{{ route('social.login', ['provider' => 'google']) }}" class="btn btn-styled btn-block btn-google btn-icon--2 btn-icon-left px-4 mb-3">
                                            <i class="icon fa fa-google"></i> {{ translate('Login with Google')}}
                                        </a>
                                    @endif
                                    @if (\App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                        <a href="{{ route('social.login', ['provider' => 'twitter']) }}" class="btn btn-styled btn-block btn-twitter btn-icon--2 btn-icon-left px-4">
                                            <i class="icon fa fa-twitter"></i> {{ translate('Login with Twitter')}}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-center px-35 pb-3">
                        <p class="text-md">
                            {{ translate('Need an account?')}} <a href="{{ route('user.registration') }}" class="strong-600">{{ translate('Register Now')}}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="signup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="text-center px-35 pt-3">
                        <h1 class="heading heading-4 strong-500">
                            {{ translate('Create an account.')}}
                        </h1>
                    </div>
                    <div class="px-5 py-3 py-lg-4">
                        <div class="">
                            <form id="reg-form" class="form-default" role="form" action="{{ route('register') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <input type="text" class="h-auto form-control-lg form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" placeholder="{{  translate('Name') }}" name="name">
                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                    @endif
                                </div>

                                @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                    <div class="form-group phone-form-group mb-1">
                                        <input type="tel" id="phone-code" class="h-auto w-100 form-control-lg form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off">
                                    </div>

                                    <input type="hidden" name="country_code" value="">

                                    <div class="form-group email-form-group mb-1 d-none">
                                        <input type="email" class="h-auto form-control-lg form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{  translate('Email') }}" name="email"  autocomplete="off">
                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <button class="btn btn-link p-0" type="button" onclick="toggleEmailPhone(this)">{{ translate('Use Email Instead') }}</button>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <input type="email" class="h-auto form-control-lg form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{  translate('Email') }}" name="email">
                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                        @endif
                                    </div>
                                @endif

                                <div class="form-group">
                                    <input type="password" class="h-auto form-control-lg form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{  translate('Password') }}" name="password">
                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <input type="password" class="h-auto form-control-lg form-control" placeholder="{{  translate('Confirm Password') }}" name="password_confirmation">
                                </div>

                                @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
                                    <div class="form-group">
                                        <div class="g-recaptcha" data-sitekey="{{ env('CAPTCHA_KEY') }}"></div>
                                    </div>
                                @endif

                                <div class="checkbox text-left">
                                    <input class="magic-checkbox" type="checkbox" name="checkbox_example_1" id="checkboxExample_1a" required>
                                    <label for="checkboxExample_1a" class="text-sm">{{ translate('By signing up you agree to our terms and conditions.')}}</label>
                                </div>

                                <div class="text-right mt-3">
                                    <button type="submit" class="btn btn-styled btn-base-1 w-100 btn-md">{{  translate('Create Account') }}</button>
                                </div>
                            </form>
                            @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                <div class="or or--1 mt-3 text-center">
                                    <span>or</span>
                                </div>
                                <div>
                                    @if (\App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1)
                                        <a href="{{ route('social.login', ['provider' => 'facebook']) }}" class="btn btn-styled btn-block btn-facebook btn-icon--2 btn-icon-left px-4 mb-3">
                                            <i class="icon fa fa-facebook"></i> {{ translate('Login with Facebook')}}
                                        </a>
                                    @endif
                                    @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1)
                                        <a href="{{ route('social.login', ['provider' => 'google']) }}" class="btn btn-styled btn-block btn-google btn-icon--2 btn-icon-left px-4 mb-3">
                                            <i class="icon fa fa-google"></i> {{ translate('Login with Google')}}
                                        </a>
                                    @endif
                                    @if (\App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                        <a href="{{ route('social.login', ['provider' => 'twitter']) }}" class="btn btn-styled btn-block btn-twitter btn-icon--2 btn-icon-left px-4">
                                            <i class="icon fa fa-twitter"></i> {{ translate('Login with Twitter')}}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-center px-35 pb-3">
                        <p class="text-md">
                            {{ translate('Already have an account?')}}<a href="{{ route('user.login') }}" class="strong-600">{{ translate('Log In')}}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $.post('{{ route('home.section.featured') }}', {_token:'{{ csrf_token() }}'}, function(data){
                $('#section_featured').html(data);
                slickInit();
            });

            $.post('{{ route('home.section.best_selling') }}', {_token:'{{ csrf_token() }}'}, function(data){
                $('#section_best_selling').html(data);
                slickInit();
            });

            $.post('{{ route('home.section.home_categories') }}', {_token:'{{ csrf_token() }}'}, function(data){
                $('#section_home_categories').html(data);
                slickInit();
            });

            @if (\App\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
            $.post('{{ route('home.section.best_sellers') }}', {_token:'{{ csrf_token() }}'}, function(data){
                $('#section_best_sellers').html(data);
                slickInit();
            });
            @endif
        });
        function loginClick(){
            $('#login').modal('show');
        }
        function signupClick(){
            $('#signup').modal('show');
        }
    </script>
@endsection
