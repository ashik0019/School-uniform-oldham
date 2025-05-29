@extends('frontend.layouts.app')

@section('content')
    <section class="gry-bg py-5">
        <div class="profile">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4 offset-xl-4">
                        <div class="card">
                            <div class="text-center px-35 pt-5">
                                <h3 class="heading heading-4 strong-500">
                                    {{ __('OTP') }}
                                </h3>
                                <p class="pad-btm">{{__('Enter your OTP to change password.')}} </p>
                            </div>
                            <div class="px-5 py-2 py-lg-2">
                                <div class="row align-items-center">
                                    <div class="col-12 col-lg">
                                        <form method="POST" action="{{ route('reset.pass.otp-check') }}">
                                            @csrf
                                            <div class="form-group">
                                                <input id="otp" type="number" class="form-control{{ $errors->has('otp') ? ' is-invalid' : '' }}" name="otp" value="{{ old('otp') }}" required placeholder="Enter your otp code">

                                                @if ($errors->has('otp'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('otp') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group text-right">
                                                <button class="btn btn-danger btn-lg btn-block" type="submit">
                                                    {{ __('Check') }}
                                                </button>
                                            </div>
                                        </form>
                                        <div class="pad-top pb-4">
                                            <a href="{{route('user.login')}}" class="btn-link text-bold text-main">{{__('Back to Login')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
