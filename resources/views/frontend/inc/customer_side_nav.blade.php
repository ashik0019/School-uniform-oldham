<style>
    .form-control {
        display: block;
        width: 100%;
        height: 35px;
        padding: 0.385rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }
</style>
<div class="sidebar sidebar--style-3 no-border stickyfill p-0">
    <div class="widget mb-0">
        <div class="widget-profile-box text-center p-3">
            @if (Auth::user()->avatar_original != null)
            <div class="image" style="background-image:url('{{ my_asset(Auth::user()->avatar_original) }}')"></div>
            @else
            <img src="{{ static_asset('frontend/images/user.png') }}" class="image rounded-circle">
            @endif
            <div class="name">{{ Auth::user()->name }}</div>
            <!-- <div class="badge badge-primary">Referral Code: {{ Auth::user()->referral_code }}</div>
            <div class="input-group mt-2" data-toggle="refer" title="Click here to copy link" data-placement="top">
                <input id="referInput2" style="font-size: 11px;" type="text" class="form-control referInput" value="{{url('/refer/'.Auth::user()->referral_code)}}" aria-describedby="basic-addon2" readonly>
                <div class="input-group-append">
                    <button class="btn btn-success" onclick="referFun2()">
                        Copy
                    </button>
                </div>
            </div> -->
        </div>
        <div class="sidebar-widget-title py-3">
            <span>{{translate('Menu')}}</span>
        </div>
        <div class="widget-profile-menu py-3">
            <ul class="categories categories--style-3">
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ areActiveRoutesHome(['dashboard'])}}">
                        <i class="la la-dashboard"></i>
                        <span class="category-name">
                            {{translate('Dashboard')}}
                        </span>
                    </a>
                </li>

                @if(\App\BusinessSetting::where('type', 'classified_product')->first()->value == 1)
                <li>
                    <a href="{{ route('customer_products.index') }}" class="{{ areActiveRoutesHome(['customer_products.index', 'customer_products.create', 'customer_products.edit'])}}">
                        <i class="la la-diamond"></i>
                        <span class="category-name">
                            {{translate('Classified Products')}}
                        </span>
                    </a>
                </li>
                @endif
                @php
                $delivery_viewed = App\Order::where('user_id', Auth::user()->id)->where('delivery_viewed', 0)->get()->count();
                $payment_status_viewed = App\Order::where('user_id', Auth::user()->id)->where('payment_status_viewed', 0)->get()->count();
                $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
                $club_point_addon = \App\Addon::where('unique_identifier', 'club_point')->first();
                @endphp
                <li>
                    <a href="{{ route('purchase_history.index') }}" class="{{ areActiveRoutesHome(['purchase_history.index'])}}">
                        <i class="la la-file-text"></i>
                        <span class="category-name">
                            {{translate('My Orders')}} @if($delivery_viewed > 0 || $payment_status_viewed > 0)<span class="ml-2" style="color:green"><strong>({{ translate('New Notifications') }})</strong></span>@endif
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('digital_purchase_history.index') }}" class="{{ areActiveRoutesHome(['digital_purchase_history.index'])}}">
                        <i class="la la-download"></i>
                        <span class="category-name">
                            {{translate('Downloads')}}
                        </span>
                    </a>
                </li>

                @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                <li>
                    <a href="{{ route('customer_refund_request') }}" class="{{ areActiveRoutesHome(['customer_refund_request'])}}">
                        <i class="la la-file-text"></i>
                        <span class="category-name">
                            {{translate('Sent Refund Request')}}
                        </span>
                    </a>
                </li>
                @endif

                <li>
                    <a href="{{ route('wishlists.index') }}" class="{{ areActiveRoutesHome(['wishlists.index'])}}">
                        <i class="la la-heart-o"></i>
                        <span class="category-name">
                            {{translate('Wishlist')}}
                        </span>
                    </a>
                </li>
                @if (\App\BusinessSetting::where('type', 'conversation_system')->first()->value == 1)
                @php
                $conversation = \App\Conversation::where('sender_id', Auth::user()->id)->where('sender_viewed', 0)->get();
                @endphp
                <li>
                    <a href="{{ route('conversations.index') }}" class="{{ areActiveRoutesHome(['conversations.index', 'conversations.show'])}}">
                        <i class="la la-comment"></i>
                        <span class="category-name">
                            {{translate('Conversations')}}
                            @if (count($conversation) > 0)
                            <span class="ml-2" style="color:green"><strong>({{ count($conversation) }})</strong></span>
                            @endif
                        </span>
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('profile') }}" class="{{ areActiveRoutesHome(['profile'])}}">
                        <i class="la la-user"></i>
                        <span class="category-name">
                            {{translate('Manage Profile')}}
                        </span>
                    </a>
                </li>
                {{--@if (\App\BusinessSetting::where('type', 'wallet_system')->first()->value == 1)
                    <li>
                        <a href="{{ route('wallet.index') }}" class="{{ areActiveRoutesHome(['wallet.index'])}}">
                <i class="la la-dollar"></i>
                <span class="category-name">
                    {{translate('My Wallet')}}
                </span>
                </a>
                </li>
                @endif--}}

                @if ($club_point_addon != null && $club_point_addon->activated == 1)
                <li>
                    <a href="{{ route('earnng_point_for_user') }}" class="{{ areActiveRoutesHome(['earnng_point_for_user'])}}">
                        <i class="la la-dollar"></i>
                        <span class="category-name">
                            {{translate('Earning Points')}}
                        </span>
                    </a>
                </li>
                @endif

                @if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated && Auth::user()->affiliate_user != null && Auth::user()->affiliate_user->status)
                <li>
                    <a href="{{ route('affiliate.user.index') }}" class="{{ areActiveRoutesHome(['affiliate.user.index', 'affiliate.payment_settings'])}}">
                        <i class="la la-dollar"></i>
                        <span class="category-name">
                            {{translate('Affiliate System')}}
                        </span>
                    </a>
                </li>
                @endif
                @php
                $support_ticket = DB::table('tickets')
                ->where('client_viewed', 0)
                ->where('user_id', Auth::user()->id)
                ->count();
                @endphp
                <li>
                    <a href="{{ route('support_ticket.index') }}" class="{{ areActiveRoutesHome(['support_ticket.index'])}}">
                        <i class="la la-support"></i>
                        <span class="category-name">
                            {{translate('Support Ticket')}} @if($support_ticket > 0)<span class="ml-2" style="color:green"><strong>({{ $support_ticket }} {{ translate('New') }})</strong></span></span>@endif
                        </span>
                    </a>
                </li>
            </ul>
        </div>
        @if (\App\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
        <div class="widget-seller-btn pt-4">
            <a href="{{ route('shops.create') }}" class="btn btn-anim-primary w-100">{{translate('Be A Seller')}}</a>
        </div>
        @endif
    </div>
</div>