@php
    $generalsetting = \App\GeneralSetting::first();
@endphp
@foreach($order_ids as $orderId)
    @php
      $order = \App\Order::find($orderId);
    @endphp
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<!--    <title>{{ $generalsetting->site_name }}</title>-->
    <title></title>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
	<style media="all">
		@font-face {
            font-family: 'Roboto';
            src: url("{{ my_asset('fonts/Roboto-Regular.ttf') }}") format("truetype");
            font-weight: normal;
            font-style: normal;
        }
        *{
            margin: 0;
            padding: 0;
            line-height: 1.3;
            font-family: 'Roboto';
            color: #333542;
        }
		body{
			font-size: .875rem;
		}
		.gry-color *,
		.gry-color{
			color:#878f9c;
		}
		table{
			width: 100%;
		}
		table th{
			font-weight: normal;
		}
		table.padding th{
			padding: .5rem .7rem;
		}
		table.padding td{
			padding: .7rem;
		}
		table.sm-padding td{
			padding: .2rem .7rem;
		}
		.border-bottom td,
		.border-bottom th{
			border-bottom:1px solid #eceff4;
		}
		.text-left{
			text-align:left;
		}
		.text-right{
			text-align:right;
		}
		.small{
			font-size: .85rem;
		}
		.currency{

		}
	</style>
</head>
<body>
    <p style="margin-left: 35%;">" বিসমিল্লাহির রাহমানির রাহীম "</p>
    <p style="margin-left: 20%;">"৫ ওয়াক্ত নামাজ আদায় করুন ৫ ওয়াক্ত নামাজ বেহেস্তের চাবি"</p>
	<div style="margin-left:auto;margin-right:auto; height:1046px;" >

		<div style="background: #eceff4;padding: 1.5rem;">
			<table>
				<tr>
					<td>
						@if($generalsetting->logo != null)
							<img loading="lazy"  src="{{ my_asset($generalsetting->logo) }}" height="40" style="display:inline-block;">
						@else
							<img loading="lazy"  src="{{ static_asset('frontend/images/logo/logo.png') }}" height="40" style="display:inline-block;">
						@endif
					</td>
					<td style="font-size: 2.5rem;" class="text-right strong">{{ translate('INVOICE') }}</td>
				</tr>
			</table>
			<table>
				<tr>
					<td style="font-size: 1.2rem;" class="strong">{{ $generalsetting->site_name }}</td>
					<td class="text-right"></td>
				</tr>
				<tr>
					<td class="gry-color small">{{ $generalsetting->address }}</td>
					<td class="text-right"></td>
				</tr>
				<tr>
					<td class="gry-color small">{{ translate('Email') }}: {{ $generalsetting->email }}</td>
					<td class="text-right small"><span class="gry-color small">{{ translate('Order ID') }}:</span> <span class="strong">{{ $order->code }}</span></td>
				</tr>
				<tr>
					<td class="gry-color small">{{ translate('Phone') }}: {{ $generalsetting->phone }}</td>
					<td class="text-right small"><span class="gry-color small">{{ translate('Order Date') }}:</span> <span class=" strong">{{ date('d-m-Y', $order->date) }}</span></td>
				</tr>
			</table>

		</div>


		<div style="padding: 1.5rem;padding-bottom: 0">
			<div class="row">
                <div class="float-left col-md-6" style="width: 56%; float: left;">
                    <table>
                        @php
                            $shipping_address = json_decode($order->shipping_address);
                        @endphp
                        <tr><td class="strong small gry-color">{{ translate('Bill to') }}:</td></tr>
                        <tr><td class="strong">{{ $shipping_address->name }}</td></tr>
                        <tr><td class="gry-color small">{{ $shipping_address->address }}, {{ $shipping_address->city }}, {{ $shipping_address->country }}</td></tr>
                        <tr><td class="gry-color small">{{ translate('Email') }}: {{ $shipping_address->email }}</td></tr>
                        <tr><td class="gry-color small">{{ translate('Phone') }}: {{ $shipping_address->phone }}</td></tr>
                    </table>
                </div>
                <div class="float-right col-md-6" style="width: 35%; float: right;">
                    <table>

                        <tr><td class="strong small gry-color">{{ translate('Payment Status') }}:</td></tr>
                        <tr><td class="gry-color small">{{ translate('Payment Method') }}: {{ $order->payment_type == 'sslcommerz' ? 'SSLCOMMERZ' : 'CASH ON DELIVERY' }}</td></tr>
                        <tr><td class="gry-color small">{{ translate('Payment Status') }}: {{ $order->payment_status }}</td></tr>

                    </table>
                </div>
            </div>
		</div>

	    <div style="padding: 1.5rem;">
			<table class="padding text-left small border-bottom">
				<thead>
	                <tr class="gry-color" style="background: #eceff4;">
	                    <th width="10%">{{ translate('Image') }}</th>
	                    <th width="30%">{{ translate('Product Name') }}</th>
<!--						<th width="10%">{{ translate('Delivery Type') }}</th>-->
	                    <th width="10%">{{ translate('Qty') }}</th>
	                    <th width="15%">{{ translate('Unit Price') }}</th>
	                    <th width="10%">{{ translate('Tax') }}</th>
	                    <th width="15%" class="text-right">{{ translate('Total') }}</th>
	                </tr>
				</thead>
				<tbody class="strong">
	                @foreach ($order->orderDetails as $key => $orderDetail)
		                @if ($orderDetail->product != null)
							<tr class="">
                                <td>
                                    <img src="{{my_asset($orderDetail->product->thumbnail_img)}}" alt="" width="50px;">
                                </td>
								<td>{{ $orderDetail->product->name }} @if($orderDetail->variation != null) ({{ $orderDetail->variation }}) @endif</td>
<!--								<td>
									@if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
										{{ translate('Home Delivery') }}
									@elseif ($orderDetail->shipping_type == 'pickup_point')
										@if ($orderDetail->pickup_point != null)
											{{ $orderDetail->pickup_point->name }} ({{ translate('Pickip Point') }})
										@endif
									@endif
								</td>-->
								<td class="gry-color">{{ $orderDetail->quantity }}</td>
								<td class="gry-color currency">@if($orderDetail->price > 0) {{ single_price($orderDetail->price/$orderDetail->quantity) }} @endif</td>
								<td class="gry-color currency">@if($orderDetail->tax > 0) {{ single_price($orderDetail->tax/$orderDetail->quantity) }} @endif</td>
			                    <td class="text-right currency">@if($orderDetail->price > 0){{ single_price($orderDetail->price+$orderDetail->tax) }}@endif</td>
							</tr>
		                @endif
					@endforeach
	            </tbody>
			</table>
		</div>

	    <div style="padding:0 1.5rem;">
	        <table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">
		        <tbody>
			        <tr>
			            <th class="gry-color text-left">{{ translate('Sub Total') }}</th>
			            <td class="currency">{{ single_price($order->orderDetails->sum('price')) }}</td>
			        </tr>
			        <tr>
			            <th class="gry-color text-left">{{ translate('Shipping Cost') }}</th>
{{--			            <td class="currency">{{ single_price($order->orderDetails->sum('shipping_cost')) }}</td>--}}
			            <td class="currency">{{ single_price($order->delivery_charge) }}</td>
			        </tr>
			        <tr class="border-bottom">
			            <th class="gry-color text-left">{{ translate('Total Tax') }}</th>
			            <td class="currency">{{ single_price($order->orderDetails->sum('tax')) }}</td>
			        </tr>
			        <tr>
			            <th class="text-left strong">{{ translate('Grand Total') }}</th>
			            <td class="currency">{{ single_price($order->grand_total) }}</td>
			        </tr>
		        </tbody>
		    </table>
	    </div>

	</div>
    <script type="text/javascript">
        window.addEventListener("load", window.print());
    </script>
</body>
</html>
@endforeach
