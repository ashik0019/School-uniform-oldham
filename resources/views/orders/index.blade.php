@extends('layouts.app')

@section('content')
@php
    $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
@endphp
<!-- Basic Data Tables -->
<!--===================================================-->
<div class="panel">
    <div class="panel-heading bord-btm clearfix pad-all h-100">
        <h3 class="panel-title pull-left pad-no">{{translate('Orders ')}} <span class="badge badge-danger">{{$orders->count()}}</span></h3>
        <div class="pull-right clearfix">
            <form class="" id="sort_orders" action="" method="GET">
                <div class="box-inline pad-rgt pull-left">
                    <div class="select" style="min-width: 300px;">
                        <select name="pagination" id="pagination" class="form-control demo-select2" onchange="sort_orders()">
                            <option {{$pagination == 50 ? 'selected' : ''}} value="50">Per page 50</option>
                            <option  {{$pagination == 100 ? 'selected' : ''}} value="100">Per page 100</option>
                            <option  {{$pagination == 200 ? 'selected' : ''}} value="200">Per page 200</option>
                            <option  {{$pagination == 300 ? 'selected' : ''}} value="300">Per page 300</option>
                            <option  {{$pagination == 400 ? 'selected' : ''}} value="400">Per page 400</option>
                            <option  {{$pagination == 500 ? 'selected' : ''}} value="500">Per page 500</option>
                            <option  {{$pagination == 600 ? 'selected' : ''}} value="600">Per page 600</option>
                            <option  {{$pagination == 700 ? 'selected' : ''}} value="700">Per page 700</option>
                            <option  {{$pagination == 800 ? 'selected' : ''}} value="800">Per page 800</option>
                            <option  {{$pagination == 900 ? 'selected' : ''}} value="900">Per page 900</option>
                            <option  {{$pagination == 1000 ? 'selected' : ''}} value="1000">Per page 1000</option>
                            <option  {{$pagination == $orders->count() ? 'selected' : ''}} value="{{$orders->count()}}">All</option>
                        </select>
                    </div>
                    <div class="select" style="min-width: 300px;">
                        <select class="form-control demo-select2" name="payment_type" id="payment_type" onchange="sort_orders()">
                            <option value="">{{translate('Filter by Payment Status')}}</option>
                            <option value="paid"  @isset($payment_status) @if($payment_status == 'paid') selected @endif @endisset>{{translate('Paid')}}</option>
                            <option value="unpaid"  @isset($payment_status) @if($payment_status == 'unpaid') selected @endif @endisset>{{translate('Un-Paid')}}</option>
                        </select>
                    </div>
                </div>
                <div class="box-inline pad-rgt pull-left">
                    <div class="select" style="min-width: 300px;">
                        <select class="form-control demo-select2" name="delivery_status" id="delivery_status" onchange="sort_orders()">
                            <option value="">{{translate('Filter by Deliver Status')}}</option>
                            <option value="pending"   @isset($delivery_status) @if($delivery_status == 'pending') selected @endif @endisset>{{translate('Pending')}}</option>
                            <option value="on_review"   @isset($delivery_status) @if($delivery_status == 'on_review') selected @endif @endisset>{{translate('On review')}}</option>
                            <option value="on_delivery"   @isset($delivery_status) @if($delivery_status == 'on_delivery') selected @endif @endisset>{{translate('On delivery')}}</option>
                            <option value="delivered"   @isset($delivery_status) @if($delivery_status == 'delivered') selected @endif @endisset>{{translate('Delivered')}}</option>
                            <option value="cancel"   @isset($delivery_status) @if($delivery_status == 'cancel') selected @endif @endisset>{{translate('Cancel')}}</option>
                        </select>
                    </div>
                </div>
                <div class="box-inline pad-rgt pull-left">
                    <div class="" style="min-width: 200px;">
                        <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="panel-body">
        <form action="{{route('orders.actions')}}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <select name="order_action" id="" class="form-control">
                        <option value="">Please Select Your Action</option>
                        <option value="pending">Pending</option>
                        <option value="on_review">On Review</option>
                        <option value="on_delivery">On Delivery</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancel">Cancel</option>
                        <option value="print_all_invoice">Print Selected Invoice</option>
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="delete">Delete Selected Invoice</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            <table class="table table-striped res-table mar-no" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="checkAll"> All
                    </th>
                    <th>#</th>
                    <th>{{translate('Is Print?')}}</th>
                    <th>{{translate('Order Code')}}</th>
                    <th>{{translate('Num. of Products')}}</th>
                    <th>{{translate('Date')}}</th>
                    <th>{{translate('Customer')}}</th>
                    <th>{{translate('Amount')}}</th>
                    <th>{{translate('Delivery Status')}}</th>
                    <th>{{translate('Payment Method')}}</th>
                    <th>{{translate('Payment Status')}}</th>
                    @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                        <th>{{translate('Refund')}}</th>
                    @endif
                    <th width="10%">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $key => $order_id)
                    @php
                        $order = \App\Order::find($order_id->id);
                    @endphp
                    @if($order != null)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-control" name="order_ids[]" value="{{$order_id->id}}">
                            </td>
                            <td>
<!--                                {{ ($key+1) + ($orders->currentPage() - 1)*$orders->perPage() }}-->
                                {{$order_id->id}}

                            </td>
                            <td>
                                @if($order->is_print == 'Printed')
                                    <span class="pull-right badge badge-info">{{ translate('Printed') }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $order->code }} @if($order->viewed == 0) <span class="pull-right badge badge-info">{{ translate('New') }}</span> @endif
                            </td>
                            <td>
                                {{ count($order->orderDetails->where('seller_id', $admin_user_id)) }}
                            </td>
                            <td>{{date('jS F Y H:i A',strtotime($order->created_at))}}</td>
                            <td>
                                @if ($order->user != null)
                                    {{ $order->user->name }}
                                @else
                                    Guest ({{ $order->guest_id }})
                                @endif
                            </td>
                            <td>
                                {{ single_price($order->orderDetails->where('seller_id', $admin_user_id)->sum('price') + $order->orderDetails->where('seller_id', $admin_user_id)->sum('tax') + $order->delivery_charge )  }}
                            </td>
                            <td>
                                @php
                                    $status = $order->orderDetails->first()->delivery_status;
                                @endphp
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </td>
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}
                            </td>
                            <td>
                                <span class="badge badge--2 mr-4">
                                    @if ($order->orderDetails->where('seller_id',  $admin_user_id)->first()->payment_status == 'paid')
                                        <i class="bg-green"></i> {{ translate('Paid') }}
                                    @else
                                        <i class="bg-red"></i> {{ translate('Unpaid') }}
                                    @endif
                                </span>
                            </td>
                            @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                                <td>
                                    @if (count($order->refund_requests) > 0)
                                        {{ count($order->refund_requests) }} {{ translate('Refund') }}
                                    @else
                                        {{ translate('No Refund') }}
                                    @endif
                                </td>
                            @endif
                            <td>
                                <div class="btn-group dropdown">
                                    <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                        {{translate('Actions')}} <i class="dropdown-caret"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a href="{{ route('orders.show', encrypt($order->id)) }}">{{translate('View')}}</a></li>
                                        <li><a href="{{ route('seller.invoice.download', $order->id) }}">{{translate('Download Invoice')}}</a></li>
                                        <li><a onclick="confirm_modal('{{route('orders.destroy', $order->id)}}');">{{translate('Delete')}}</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        </form>
        <div class="clearfix">
            <div class="pull-right">
                {{ $orders->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>

@endsection



@section('script')
    <script type="text/javascript">
        function sort_orders(el){
            $('#sort_orders').submit();
        }
        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
    </script>
@endsection
