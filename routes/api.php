<?php

Route::prefix('version1/auth')->group(function () {
    Route::post('login', 'Api\AuthController@login');
    Route::post('mobile/login', 'Api\AuthController@mobileLogin');
    Route::post('signup', 'Api\AuthController@signup');
    Route::post('signup/via/phone', 'Api\AuthController@signupViaPhone');
    Route::post('social-login', 'Api\AuthController@socialLogin');
    Route::post('password/create', 'Api\PasswordResetController@create');
    Route::post('password/forget/new/store', 'Api\PasswordResetController@forgetPassCreate');
    Route::post('otp/send', 'Api\OtpVerificationController@OtpSend');
    Route::post('otp/checked', 'Api\OtpVerificationController@OtpCheck');
    Route::middleware('auth:api')->group(function () {
        Route::get('logout', 'Api\AuthController@logout');
        Route::get('user', 'Api\AuthController@user');
        Route::get('get/balance', 'Api\AuthController@getBalance');
        Route::get('get/dashboard-data', 'Api\AuthController@getDashboardData');
        Route::post('change-password', 'Api\AuthController@changePass');
    });
});

Route::prefix('version1')->group(function () {
    Route::post('/unsigned-fcm-token-store', 'Api\FcmTokenController@storeUnsigned');
    Route::post('/signed-fcm-token-store', 'Api\FcmTokenController@storeSigned');
    Route::get('/send-otp', 'Api\FcmTokenController@sendOtp');

    Route::apiResource('banners', 'Api\BannerController')->only('index');
    Route::apiResource('support_ticket', 'Api\SupportTicketController')->middleware('auth:api');
    Route::post('support_ticket/reply', 'Api\SupportTicketController@seller_store')->name('support_ticket.seller_store')->middleware('auth:api');
    Route::get('brands/top', 'Api\BrandController@top');
    Route::apiResource('brands', 'Api\BrandController')->only('index');
    Route::apiResource('business-settings', 'Api\BusinessSettingController')->only('index');
    Route::get('business-settings/about', 'Api\BusinessSettingController@about')->name('business-settings.about');
    Route::get('business-settings/contact', 'Api\BusinessSettingController@getContactUs')->name('business-settings.contact');
    Route::get('business-settings/delivery-charge', 'Api\BusinessSettingController@getDeliveryCharge')->name('business-settings.delivery-charge');
    Route::get('business-settings/showroom-location', 'Api\BusinessSettingController@getShowroomLocation')->name('business-settings.showroom-location');

    Route::get('categories/featured', 'Api\CategoryController@featured');
    Route::get('categories/home', 'Api\CategoryController@home');
    Route::apiResource('categories', 'Api\CategoryController')->only('index');
    Route::get('sub-categories/{id}', 'Api\SubCategoryController@index')->name('subCategories.index');

    Route::apiResource('colors', 'Api\ColorController')->only('index');

    Route::apiResource('currencies', 'Api\CurrencyController')->only('index');

    Route::apiResource('customers', 'Api\CustomerController')->only('show');

    Route::apiResource('general-settings', 'Api\GeneralSettingController')->only('index');

    Route::apiResource('home-categories', 'Api\HomeCategoryController')->only('index');

    Route::get('purchase-history/{id}', 'Api\PurchaseHistoryController@index')->middleware('auth:api');
    Route::get('purchase-history-details/{id}', 'Api\PurchaseHistoryDetailController@index')->name('purchaseHistory.details')->middleware('auth:api');

    Route::get('products/admin', 'Api\ProductController@admin');
    Route::get('products/seller', 'Api\ProductController@seller');
    Route::get('products/category/{id}', 'Api\ProductController@category')->name('api.products.category');
    Route::get('products/sub-category/{id}', 'Api\ProductController@subCategory')->name('products.subCategory');
    Route::get('products/sub-sub-category/{id}', 'Api\ProductController@subSubCategory')->name('products.subSubCategory');
    Route::get('products/brand/{id}', 'Api\ProductController@brand')->name('api.products.brand');
    Route::get('products/todays-deal', 'Api\ProductController@todaysDeal');
    Route::get('products/flash-deal', 'Api\ProductController@flashDeal');
    Route::get('products/featured', 'Api\ProductController@featured');
    Route::get('products/best-seller', 'Api\ProductController@bestSeller');
    Route::get('products/related/{id}', 'Api\ProductController@related')->name('products.related');
    Route::get('products/top-from-seller/{id}', 'Api\ProductController@topFromSeller')->name('products.topFromSeller');
    Route::get('products/search', 'Api\ProductController@search');
    Route::get('products/suggested', 'Api\ProductController@suggestedProducts');
    Route::get('products/get-filters', 'Api\ProductController@getFilter');
    Route::get('products/filtered-products', 'Api\ProductController@filteredProducts');
    Route::get('products/get-home-products', 'Api\ProductController@getAllHomeProducts');
    Route::post('products/variant/price', 'Api\ProductController@variantPrice');
    Route::get('products/home', 'Api\ProductController@home');
    Route::apiResource('products', 'Api\ProductController')->except(['store', 'update', 'destroy']);

    Route::get('carts/{id}', 'Api\CartController@index')->middleware('auth:api');
    Route::post('carts/add', 'Api\CartController@add')->middleware('auth:api');
    Route::post('carts/change-quantity', 'Api\CartController@changeQuantity')->middleware('auth:api');
    Route::apiResource('carts', 'Api\CartController')->only('destroy')->middleware('auth:api');

    Route::get('reviews/product/{id}', 'Api\ReviewController@index')->name('api.reviews.index');
    Route::post('reviews/product/store', 'Api\ReviewController@store')->name('api.reviews.index');

    Route::get('shop/user/{id}', 'Api\ShopController@shopOfUser')->middleware('auth:api');
    Route::get('shops/details/{id}', 'Api\ShopController@info')->name('shops.info');
    Route::get('shops/products/all/{id}', 'Api\ShopController@allProducts')->name('shops.allProducts');
    Route::get('shops/products/top/{id}', 'Api\ShopController@topSellingProducts')->name('shops.topSellingProducts');
    Route::get('shops/products/featured/{id}', 'Api\ShopController@featuredProducts')->name('shops.featuredProducts');
    Route::get('shops/products/new/{id}', 'Api\ShopController@newProducts')->name('shops.newProducts');
    Route::get('shops/brands/{id}', 'Api\ShopController@brands')->name('shops.brands');
    Route::apiResource('shops', 'Api\ShopController')->only('index');

    Route::apiResource('sliders', 'Api\SliderController')->only('index');

    Route::get('wishlists/{id}', 'Api\WishlistController@index')->middleware('auth:api');
    Route::post('wishlists/check-product', 'Api\WishlistController@isProductInWishlist')->middleware('auth:api');
    Route::apiResource('wishlists', 'Api\WishlistController')->except(['index', 'update', 'show'])->middleware('auth:api');

    Route::apiResource('settings', 'Api\SettingsController')->only('index');

    Route::get('policies/seller', 'Api\PolicyController@sellerPolicy')->name('policies.seller');
    Route::get('policies/support', 'Api\PolicyController@supportPolicy')->name('policies.support');
    Route::get('policies/return', 'Api\PolicyController@returnPolicy')->name('policies.return');
    Route::get('policies/terms', 'Api\PolicyController@termsPolicy')->name('policies.terms');
    Route::get('policies/privacy', 'Api\PolicyController@privacyPolicy')->name('policies.privacy');
    Route::get('policies/faq', 'Api\PolicyController@faqPolicy')->name('policies.faq');

    Route::get('user/info/{id}', 'Api\UserController@info')->middleware('auth:api');
    Route::post('user/info/update', 'Api\UserController@updateName')->middleware('auth:api');
    Route::get('user/shipping/address/{id}', 'Api\AddressController@addresses')->middleware('auth:api');
    Route::post('user/shipping/create', 'Api\AddressController@createShippingAddress')->middleware('auth:api');
    Route::get('user/shipping/delete/{id}', 'Api\AddressController@deleteShippingAddress')->middleware('auth:api');
    Route::get('user/shipping/edit/{id}', 'Api\AddressController@deleteShippingAddressEdit')->middleware('auth:api');
    Route::post('user/shipping/make-as-default', 'Api\AddressController@addressMakeDefault')->middleware('auth:api');

    Route::post('coupon/apply', 'Api\CouponController@apply')->middleware('auth:api');

    Route::post('payments/pay/stripe', 'Api\StripeController@processPayment')->middleware('auth:api');
    Route::post('payments/pay/paypal', 'Api\PaypalController@processPayment')->middleware('auth:api');
    Route::post('payments/pay/wallet', 'Api\WalletController@processPayment')->middleware('auth:api');
    Route::post('payments/pay/cod', 'Api\PaymentController@cashOnDelivery')->middleware('auth:api');

    Route::post('order/store', 'Api\OrderController@store')->middleware('auth:api');
    Route::get('order/cancel/{id}', 'Api\OrderController@orderCancel')->middleware('auth:api');
    Route::get('order/track/{order_code}', 'Api\OrderController@trackOrder');

    Route::get('wallet/balance/{id}', 'Api\WalletController@balance')->middleware('auth:api');
    Route::get('wallet/history/{id}', 'Api\WalletController@walletRechargeHistory')->middleware('auth:api');
});

Route::post('/checkout/ssl/pay', 'Api\PublicSslCommerzPaymentController@index');
Route::POST('/success', 'Api\PublicSslCommerzPaymentController@success');
Route::POST('/fail', 'Api\PublicSslCommerzPaymentController@fail');
Route::POST('/cancel', 'Api\PublicSslCommerzPaymentController@cancel');
Route::POST('/ipn', 'Api\PublicSslCommerzPaymentController@ipn');
Route::get('/ssl/redirect/{status}', 'Api\PublicSslCommerzPaymentController@status')->name('ssl-redirect');
Route::get('/web/payment/{status}', 'Api\PublicSslCommerzPaymentController@statusWeb');


Route::fallback(function () {
    return response()->json([
        'data' => [],
        'success' => false,
        'status' => 404,
        'message' => 'Invalid Route'
    ]);
});
