@extends('frontend.layouts.app')
@section('css')
    <style>
        input::-webkit-input-placeholder {
            font-size: 12px;
            line-height: 3;
        }
    </style>
@endsection
@section('content')
    <section class="gry-bg py-5">
        <div class="profile">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4 offset-xl-4">
                        <div class="card">
                            <div class="text-center px-35 pt-3">
                                <div>
                                    <img src="{{asset('public/frontend/images/mixed/send_sms.png')}}" width="70" height="70" alt="send_sms">
                                </div>
                                <h3 class="heading heading-4 strong-500">
                                    {{__('Mobile Number Verification')}}
                                </h3>
                                <p class="px-4">We have sent verification code to your mobile. Please enter verification code here. </p>
                            </div>
                            <div class="px-5">
                                <div class="row align-items-center">
                                    <div class="col-12 col-lg">
                                        <form class="form-default" role="form" action="{{ route('get-verification-code-store') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <input type="hidden" name="phone" value="{{$verCode}}">
                                                        <div class="form-fild">
                                                            <input name="code" value="" type="number" class="form-control" maxlength="4" placeholder="Enter verification code">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row align-items-center">
                                                <div class="col-12 text-right">
                                                    <button type="submit" class="btn btn-base-1 w-100 btn-md">{{ __('VERIFY') }}</button>
                                                </div>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                            <div class="text-center px-35 pb-3 mt-3">
                                <p class="text-md">
                                    <span><span style="font-size: 12px;">Didn't Receive the Code?</span> <a href="">Resend Code</a></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="bg-white p-4 mx-auto mt-4">
                        <div class="">
                            <table class="table table-responsive table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <td>{{__('Seller Account')}}</td>
                                        <td><button class="btn btn-info" onclick="autoFillSeller()">Copy credentials</button></td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Customer Account')}}</td>
                                        <td><button class="btn btn-info" onclick="autoFillCustomer()">Copy credentials</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> --}}

                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script type="text/javascript">
        function autoFillSeller(){
            $('#email').val('seller@example.com');
            $('#password').val('123456');
        }
        function autoFillCustomer(){
            $('#email').val('customer@example.com');
            $('#password').val('123456');
        }
    </script>
@endsection
