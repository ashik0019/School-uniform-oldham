
<style>
    .nav-pills .nav-item:first-child .nav-link {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    .nav-pills {
        border: 0 solid transparent;
        border-radius: 0;
    }
    .nav-pills .nav-item:last-child .nav-link {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    .btn:not(:disabled):not(.disabled):hover {
        background-image: none;
        color: #f79f1f;;
    }
    .sub-sub-category-list li a:hover{
        color: #f79f1f;;
    }
    .side-user-menu li a:hover{
        color: #f79f1f;;
    }
    @media (max-width: 991px){
        .side-menu-list ul {
             padding: 0px 0;
            margin: 0;
            list-style: none;
        }
    }

</style>
<div class="header bg-white">
    <!-- Top Bar -->
    <div class="top-navbar">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col">
                    <ul class="inline-links d-lg-inline-block d-flex justify-content-between">
                        <li class="dropdown" id="lang-change">
                            @php
                                if(Session::has('locale')){
                                    $locale = Session::get('locale', Config::get('app.locale'));
                                }
                                else{
                                    $locale = 'en';
                                }
                            @endphp
                            {{--                            <a href="" class="dropdown-toggle top-bar-item" data-toggle="dropdown">--}}
                            {{--                                <img src="{{ asset('frontend/images/icons/flags/'.$locale.'.png') }}" class="flag"><span class="language">{{ \App\Language::where('code', $locale)->first()->name }}</span>--}}
                            {{--                            </a>--}}
                            {{--                            <ul class="dropdown-menu">--}}
                            {{--                                @foreach (\App\Language::all() as $key => $language)--}}
                            {{--                                    <li class="dropdown-item @if($locale == $language) active @endif">--}}
                            {{--                                        <a href="#" data-flag="{{ $language->code }}"><img src="{{ asset('frontend/images/icons/flags/'.$language->code.'.png') }}" class="flag"><span class="language">{{ $language->name }}</span></a>--}}
                            {{--                                    </li>--}}
                            {{--                                @endforeach--}}
                            {{--                            </ul>--}}
                        </li>

                        <li class="dropdown" id="currency-change">
                            @php
                                if(Session::has('currency_code')){
                                    $currency_code = Session::get('currency_code', $code);
                                }
                                else{
                                    $currency_code = \App\Currency::findOrFail(\App\BusinessSetting::where('type', 'system_default_currency')->first()->value)->code;
                                }
                            @endphp
                            {{--                            <a href="" class="dropdown-toggle top-bar-item" data-toggle="dropdown">--}}
                            {{--                                {{ \App\Currency::where('code', $currency_code)->first()->name }} {{ (\App\Currency::where('code', $currency_code)->first()->symbol) }}--}}
                            {{--                            </a>--}}
                            {{--                            <ul class="dropdown-menu">--}}
                            {{--                                @foreach (\App\Currency::where('status', 1)->get() as $key => $currency)--}}
                            {{--                                    <li class="dropdown-item @if($currency_code == $currency->code) active @endif">--}}
                            {{--                                        <a href="" data-currency="{{ $currency->code }}">{{ $currency->name }} ({{ $currency->symbol }})</a>--}}
                            {{--                                    </li>--}}
                            {{--                                @endforeach--}}
                            {{--                            </ul>--}}
                        </li>
                        <li>
                            <strong class="top-bar-item"><i class="fa fa-phone"></i> Hotline: 0000 | <i class="fa fa-clock-o"></i> 9 AM to 11 PM</strong>
                        </li>
                    </ul>
                </div>

                <div class="col-5 text-right d-none d-lg-block">
                    <ul class="inline-links">
                        <li>
                            <a href="{{ route('orders.track') }}" class="top-bar-item">{{__('Track Order')}}</a>
                        </li>
                        @auth
                            <li>
                                <a href="{{ route('dashboard') }}" class="top-bar-item">{{__(Auth::user()->name)}}'s Account</a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}" class="top-bar-item">{{__('Logout')}}</a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('user.login') }}" class="top-bar-item">{{__('Login')}}</a>
                            </li>
                            <li>
                                <a href="{{ route('user.registration') }}" class="top-bar-item">{{__('Registration')}}</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- END Top Bar -->

    <!-- mobile menu -->
    <div class="mobile-side-menu d-lg-none">
        <div class="side-menu-overlay opacity-0" onclick="sideMenuClose()"></div>
        <div class="side-menu-wrap opacity-0">
            <div class="side-menu closed">
                <div class="side-menu-header ">
                    <div class="side-menu-close" onclick="sideMenuClose()">
                        <i class="la la-close"></i>
                    </div>

                    @auth
                        <div class="widget-profile-box px-3 py-3 d-flex align-items-center">
                            <div class="image " style="background-image:url('{{ url('/public/'.Auth::user()->avatar_original) }}')"></div>
                            <div class="name">
                                {{ Auth::user()->name }}<br>
                                <div class="badge badge-primary">Referral code: {{ Auth::user()->referral_code }}</div>
                            </div>
                        </div>
                        <div class="input-group mb-2 px-2" data-toggle="refer" title="Click here to copy link" data-placement="top">
                            <input id="referInput" style="font-size: 11px;" type="text" class="form-control" value="{{url('/refer/'.Auth::user()->referral_code)}}" aria-describedby="basic-addon2" readonly>
                            <div class="input-group-append">
                                <button  class="btn btn-success" onclick="referFun()">
                                    Copy
                                </button>
                            </div>
                        </div>
                        <div class="side-login px-3 pb-3">
                            <a class="btn btn-danger" href="{{ route('logout') }}">{{__('Sign Out')}}</a>
                        </div>
                    @else
                        <div class="widget-profile-box px-3 py-4 d-flex align-items-center">
                            <div class="image " style="background-image:url('{{ asset('frontend/images/icons/user-placeholder.jpg') }}')"></div>
                        </div>
                        <div class="side-login px-3 pb-3">
                            <a class="btn btn-primary" href="{{ route('user.login') }}">{{__('Sign In')}}</a>
                            <a class="btn btn-success" href="{{ route('user.registration') }}">{{__('Registration')}}</a>
                        </div>
                    @endauth
                </div>

                <div class="text-center">
                    <ul class="nav nav-pills  my-0" id="myTab" role="tablist">
                        <li class="nav-item bg-white shadow-sm" role="presentation" style="margin-left: 6%;">
                            <a class="nav-link active shadow-sm " id="home-tab" data-toggle="tab" href="#category" role="tab" aria-controls="home" aria-selected="true" ><i class="fa fa-list-alt"></i> CATEGORIES</a>
                        </li>
                        <li class="nav-item bg-white shadow-sm" role="presentation">
                            <a class="nav-link shadow-sm" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false" ><i class="fa fa-dashboard"></i> DASHBOARD</a>
                        </li>
                    </ul>
                </div>
                <div class="side-menu-list px-3">

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="category" role="tabpanel" aria-labelledby="home-tab">
                            <div class="category-accordion py-3">
                                {{--<div class="sidebar-widget-title py-3">
                                    <span>Categories</span>
                                </div>--}}
                                @foreach (\App\Category::all() as $key => $category)
                                    <div class="single-category">
                                        <button class="btn w-100 category-name collapsed" type="button" data-toggle="collapse" data-target="#categoryy-{{ $key }}" aria-expanded="true" style="cursor: pointer;">
                                            {{ __($category->name) }}
                                        </button>

                                        <div id="categoryy-{{ $key }}" class="collapse">
                                            @foreach ($category->subcategories as $key2 => $subcategory)
                                                <div class="single-sub-category">
                                                    <button class="btn w-100 sub-category-name" type="button" data-toggle="collapse" data-target="#subCategoryy-{{ $key }}-{{ $key2 }}" aria-expanded="true" style="cursor: pointer;">
                                                        {{ __($subcategory->name) }}
                                                    </button>
                                                    <div id="subCategoryy-{{ $key }}-{{ $key2 }}" class="collapse">
                                                        <ul class="sub-sub-category-list">
                                                            @foreach ($subcategory->subsubcategories as $key3 => $subsubcategory)
                                                                <li><a href="{{ route('products.subsubcategory', $subsubcategory->slug) }}" style="cursor: pointer;">{{ __($subsubcategory->name) }}</a></li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <ul class="side-user-menu">
                                <li>
                                    <a href="{{ route('home') }}">
                                        <i class="la la-home"></i>
                                        <span>{{__('Home')}}</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('dashboard') }}">
                                        <i class="la la-dashboard"></i>
                                        <span>{{__('Dashboard')}}</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('purchase_history.index') }}">
                                        <i class="la la-file-text"></i>
                                        <span>{{__('Purchase History')}}</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('compare') }}">
                                        <i class="la la-refresh"></i>
                                        <span>{{__('Compare')}}</span>
                                        @if(Session::has('compare'))
                                            <span class="badge" id="compare_items_sidenav">{{ count(Session::get('compare'))}}</span>
                                        @else
                                            <span class="badge" id="compare_items_sidenav">0</span>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('cart') }}">
                                        <i class="la la-shopping-cart"></i>
                                        <span>{{__('Cart')}}</span>
                                        @if(Session::has('cart'))
                                            <span class="badge" id="cart_items_sidenav">{{ count(Session::get('cart'))}}</span>
                                        @else
                                            <span class="badge" id="cart_items_sidenav">0</span>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('wishlists.index') }}">
                                        <i class="la la-heart-o"></i>
                                        <span>{{__('Wishlist')}}</span>
                                    </a>
                                </li>

                                @if (\App\BusinessSetting::where('type', 'wallet_system')->first()->value == 1)
                                    <li>
                                        <a href="{{ route('wallet.index') }}">
                                            <i class="la la-dollar"></i>
                                            <span>{{__('My Wallet')}}</span>
                                        </a>
                                    </li>
                                @endif

                                <li>
                                    <a href="{{ route('profile') }}">
                                        <i class="la la-user"></i>
                                        <span>{{__('Manage Profile')}}</span>
                                    </a>
                                </li>

{{--                                @if (\App\BusinessSetting::where('type', 'wallet_system')->first()->value == 1)--}}
{{--                                    <li>--}}
{{--                                        <a href="{{ route('reference.index') }}" class="{{ areActiveRoutesHome(['reference.index'])}}">--}}
{{--                                            <i class="la la-users"></i>--}}
{{--                                            <span class="category-name">--}}
{{--                                        {{__('My Reference')}}--}}
{{--                                    </span>--}}
{{--                                        </a>--}}
{{--                                    </li>--}}
{{--                                @endif--}}
                                <li>
                                    <a href="{{ route('support_ticket.index') }}" class="{{ areActiveRoutesHome(['support_ticket.index', 'support_ticket.show'])}}">
                                        <i class="la la-support"></i>
                                        <span class="category-name">
                                    {{__('Support Ticket')}}
                                </span>
                                    </a>
                                </li>

                            </ul>
                            @if (Auth::check() && Auth::user()->user_type == 'seller')
                                <div class="sidebar-widget-title py-0">
                                    <span>{{__('Shop Options')}}</span>
                                </div>
                                <ul class="side-seller-menu">
                                    <li>
                                        <a href="{{ route('seller.products') }}">
                                            <i class="la la-diamond"></i>
                                            <span>{{__('Products')}}</span>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ route('orders.index') }}">
                                            <i class="la la-file-text"></i>
                                            <span>{{__('Orders')}}</span>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ route('shops.index') }}">
                                            <i class="la la-cog"></i>
                                            <span>{{__('Shop Setting')}}</span>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ route('payments.index') }}">
                                            <i class="la la-cc-mastercard"></i>
                                            <span>{{__('Payment History')}}</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="sidebar-widget-title py-0">
                                    <span>{{__('Earnings')}}</span>
                                </div>
                                <div class="widget-balance py-3">
                                    <div class="text-center">
                                        <div class="heading-4 strong-700 mb-4">
                                            @php
                                                $orderDetails = \App\OrderDetail::where('seller_id', Auth::user()->id)->where('created_at', '>=', date('-30d'))->get();
                                                $total = 0;
                                                foreach ($orderDetails as $key => $orderDetail) {
                                                    if($orderDetail->order->payment_status == 'paid'){
                                                        $total += $orderDetail->price;
                                                    }
                                                }
                                            @endphp
                                            <small class="d-block text-sm alpha-5 mb-2">{{__('Your earnings (current month)')}}</small>
                                            <span class="p-2 bg-base-1 rounded">{{ single_price($total) }}</span>
                                        </div>
                                        <table class="text-left mb-0 table w-75 m-auto">
                                            <tbody>
                                            <tr>
                                                @php
                                                    $orderDetails = \App\OrderDetail::where('seller_id', Auth::user()->id)->get();
                                                    $total = 0;
                                                    foreach ($orderDetails as $key => $orderDetail) {
                                                        if($orderDetail->order->payment_status == 'paid'){
                                                            $total += $orderDetail->price;
                                                        }
                                                    }
                                                @endphp
                                                <td class="p-1 text-sm">
                                                    {{__('Total earnings')}}:
                                                </td>
                                                <td class="p-1">
                                                    {{ single_price($total) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                @php
                                                    $orderDetails = \App\OrderDetail::where('seller_id', Auth::user()->id)->where('created_at', '>=', date('-60d'))->where('created_at', '<=', date('-30d'))->get();
                                                    $total = 0;
                                                    foreach ($orderDetails as $key => $orderDetail) {
                                                        if($orderDetail->order->payment_status == 'paid'){
                                                            $total += $orderDetail->price;
                                                        }
                                                    }
                                                @endphp
                                                <td class="p-1 text-sm">
                                                    {{__('Last Month earnings')}}:
                                                </td>
                                                <td class="p-1">
                                                    {{ single_price($total) }}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>



                    {{--<ul class="side-seller-menu">
                        @foreach (\App\Category::all() as $key => $category)
                            <li>
                            <a href="{{ route('products.category', $category->slug) }}" class="text-truncate">
                                <img class="cat-image" src="{{ asset($category->icon) }}" width="13">
                                <span>{{ __($category->name) }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>--}}
                </div>
            </div>
        </div>
    </div>
    <!-- end mobile menu -->

    <div class="position-relative logo-bar-area">
        <div class="">
            <div class="container">
                <div class="row no-gutters align-items-center">
                    <div class="col-lg-3 col-6">
                        <div class="d-flex">
                            <div class="d-block d-lg-none mobile-menu-icon-box">
                                <!-- Navbar toggler  -->
                                <a href="" onclick="sideMenuOpen(this)">
                                    <div class="hamburger-icon">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                </a>
                            </div>

                            <!-- Brand/Logo -->
                            <a class="navbar-brand w-100" href="{{ route('home') }}">
                                @php
                                    $generalsetting = \App\GeneralSetting::first();
                                @endphp
                                @if($generalsetting->logo != null)
                                    <img src="{{ my_asset($generalsetting->logo) }}" alt="{{ env('APP_NAME') }}">
                                @else
                                    <img src="{{ static_asset('frontend/images/logo/logo.png') }}" alt="{{ env('APP_NAME') }}">
                                @endif
                            </a>

                            @if(Route::currentRouteName() != 'home' && Route::currentRouteName() != 'categories.all')
                                <div class="d-none d-xl-block category-menu-icon-box">
                                    <div class="dropdown-toggle navbar-light category-menu-icon" id="category-menu-icon">
                                        <span class="navbar-toggler-icon"></span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-9 col-6 position-static">
                        <div class="d-flex w-100">
                            <div class="search-box flex-grow-1 px-1">
                                <form action="{{ route('search') }}" method="GET">
                                    <div class="d-flex position-relative">
                                        <div class="d-lg-none search-box-back">
                                            <button class="" type="button"><i class="la la-long-arrow-left"></i></button>
                                        </div>
                                        <div class="w-100">
                                            <input type="text" aria-label="Search" id="search" name="q" class="w-100" placeholder="{{translate('Search Product here..')}}" autocomplete="off">
                                        </div>
                                        <div class="form-group category-select d-none d-xl-block">
                                            <select class="form-control selectpicker" name="category">
                                                <option value="">{{translate('All Categories')}}</option>
                                                @foreach (\App\Category::all() as $key => $category)
                                                <option value="{{ $category->slug }}"
                                                    @isset($category_id)
                                                        @if ($category_id == $category->id)
                                                            selected
                                                        @endif
                                                    @endisset
                                                    >{{ __($category->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button class="d-none d-lg-block" type="submit">
                                            <i class="la la-search la-flip-horizontal"></i>
                                        </button>
                                        <div class="typed-search-box d-none">
                                            <div class="search-preloader">
                                                <div class="loader"><div></div><div></div><div></div></div>
                                            </div>
                                            <div class="search-nothing d-none">

                                            </div>
                                            <div id="search-content">

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- <div class="d-lg-inline-block d-none">
                                <div>
                                    <a target="_blank" href="https://play.google.com/store/apps/details?id=com.lifeok.shop" class="nav-box-link">
                                        <img src="{{my_asset('img/google-play.svg')}}" alt="">
                                    </a>
                                </div>
                            </div> -->
                            <!-- <div class="d-lg-inline-block ml-2 d-none">
                                <div>
                                    <a target="_blank" href="https://apps.apple.com/us/app/life-ok-shop/id1571960665#?platform=iphone" class="nav-box-link">
                                        <img src="{{my_asset('img/apple.svg')}}" alt="">
                                    </a>
                                </div>
                            </div> -->
                            <div class="logo-bar-icons d-inline-block ml-auto">
                                <div class="d-inline-block d-lg-none">
                                    <div class="nav-search-box">
                                        <a href="#" class="nav-box-link">
                                            <i class="la la-search la-flip-horizontal d-inline-block nav-box-icon"></i>
                                        </a>
                                    </div>
                                </div>


<!--                                <div class="d-none d-lg-inline-block">
                                    <div class="nav-compare-box" id="compare">
                                        <a href="{{ route('compare') }}" class="nav-box-link">
                                            <i class="la la-refresh d-inline-block nav-box-icon"></i>
                                            <span class="nav-box-text d-none d-xl-inline-block">{{translate('Compare')}}</span>
                                            @if(Session::has('compare'))
                                                <span class="nav-box-number">{{ count(Session::get('compare'))}}</span>
                                            @else
                                                <span class="nav-box-number">0</span>
                                            @endif
                                        </a>
                                    </div>
                                </div>-->
                                <div class="d-none d-lg-inline-block">
                                    <div class="nav-wishlist-box" id="wishlist">
                                        <a href="{{ route('wishlists.index') }}" class="nav-box-link">
                                            <i class="la la-heart-o d-inline-block nav-box-icon"></i>
<!--                                            <span class="nav-box-text d-none d-xl-inline-block">{{translate('Wishlist')}}</span>-->
                                            @if(Auth::check())
                                               <span class="nav-box-number">{{ count(Auth::user()->wishlists)}}</span>
                                            @else
                                                <span class="nav-box-number">0</span>
                                            @endif
                                        </a>
                                    </div>
                                </div>
                                <div class="d-inline-block" data-hover="dropdown">
                                    <div class="nav-cart-box dropdown" id="cart_items">
                                        <a href="" class="nav-box-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="la la-shopping-cart d-inline-block nav-box-icon"></i>
<!--                                            <span class="nav-box-text d-none d-xl-inline-block">{{translate('Cart')}}</span>-->
                                            @if(Session::has('cart'))
                                                <span class="nav-box-number">{{ count(Session::get('cart'))}}</span>
                                            @else
                                                <span class="nav-box-number">0</span>
                                            @endif
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right px-0">
                                            <li>
                                                <div class="dropdown-cart px-0">
                                                    @if(Session::has('cart'))
                                                        @if(count($cart = Session::get('cart')) > 0)
                                                            <div class="dc-header">
                                                                <h3 class="heading heading-6 strong-700">{{translate('Cart Items')}}</h3>
                                                            </div>
                                                            <div class="dropdown-cart-items c-scrollbar">
                                                                @php
                                                                    $total = 0;
                                                                @endphp
                                                                @foreach($cart as $key => $cartItem)
                                                                    @php
                                                                        $product = \App\Product::find($cartItem['id']);
                                                                        $total = $total + $cartItem['price']*$cartItem['quantity'];
                                                                    @endphp
                                                                    <div class="dc-item">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="dc-image">
                                                                                <a href="{{ route('product', $product->slug) }}">
                                                                                    <img src="{{ static_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($product->thumbnail_img) }}" class="img-fluid lazyload" alt="{{ __($product->name) }}">
                                                                                </a>
                                                                            </div>
                                                                            <div class="dc-content">
                                                                                <span class="d-block dc-product-name text-capitalize strong-600 mb-1">
                                                                                    <a href="{{ route('product', $product->slug) }}">
                                                                                        {{ __($product->name) }}
                                                                                    </a>
                                                                                </span>

                                                                                <span class="dc-quantity">x{{ $cartItem['quantity'] }}</span>
                                                                                <span class="dc-price">{{ single_price($cartItem['price']*$cartItem['quantity']) }}</span>
                                                                            </div>
                                                                            <div class="dc-actions">
                                                                                <button onclick="removeFromCart({{ $key }})">
                                                                                    <i class="la la-close"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="dc-item py-3">
                                                                <span class="subtotal-text">{{translate('Subtotal')}}</span>
                                                                <span class="subtotal-amount">{{ single_price($total) }}</span>
                                                            </div>
                                                            <div class="py-2 text-center dc-btn">
                                                                <ul class="inline-links inline-links--style-3">
                                                                    <li class="px-1">
                                                                        <a href="{{ route('cart') }}" class="link link--style-1 text-capitalize btn btn-base-1 px-3 py-1">
                                                                            <i class="la la-shopping-cart"></i> {{translate('View cart')}}
                                                                        </a>
                                                                    </li>
                                                                    @if (Auth::check())
                                                                    <li class="px-1">
                                                                        <a href="{{ route('checkout.shipping_info') }}" class="link link--style-1 text-capitalize btn btn-base-1 px-3 py-1 light-text">
                                                                            <i class="la la-mail-forward"></i> {{translate('Checkout')}}
                                                                        </a>
                                                                    </li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        @else
                                                            <div class="dc-header">
                                                                <h3 class="heading heading-6 strong-700">{{translate('Your Cart is empty')}}</h3>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="dc-header">
                                                            <h3 class="heading heading-6 strong-700">{{translate('Your Cart is empty')}}</h3>
                                                        </div>
                                                    @endif
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                @if(!Auth::check())
                                    <div class="d-inline-block d-lg-none">
                                        <div class="nav-cart-box">
                                            <a href="{{route('user.login')}}" class="nav-box-link" title="Singin here..">
                                                <i class="la la-lock la-flip-horizontal d-inline-block nav-box-icon"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hover-category-menu" id="hover-category-menu">
            <div class="container">
                <div class="row no-gutters position-relative">
                    <div class="col-lg-3 position-static">
                        <div class="category-sidebar" id="category-sidebar">
                            <div class="all-category">
                                <span>{{translate('CATEGORIES')}}</span>
                                <a href="{{ route('categories.all') }}" class="d-inline-block">{{ translate('See All') }} ></a>
                            </div>
                            <ul class="categories">
                                @foreach (\App\Category::all()->take(11) as $key => $category)
                                    @php
                                        $brands = array();
                                    @endphp
                                    <li class="category-nav-element" data-id="{{ $category->id }}">
                                        <a href="{{ route('products.category', $category->slug) }}">
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

                </div>
            </div>
        </div>
    </div>
    <!-- Navbar -->

    <!-- <div class="main-nav-area d-none d-lg-block">
        <nav class="navbar navbar-expand-lg navbar--bold navbar--style-2 navbar-light bg-default">
            <div class="container">
                <div class="collapse navbar-collapse align-items-center justify-content-center" id="navbar_main">
                    <ul class="navbar-nav">
                        @foreach (\App\Search::orderBy('count', 'desc')->get()->take(5) as $key => $search)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('suggestion.search', $search->query) }}">{{ $search->query }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </nav>
    </div> -->
</div>
