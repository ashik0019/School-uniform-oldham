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
                                    {{ __('New Password') }}
                                </h3>
                                <p class="pad-btm">{{__('Enter your new password to change it.')}} </p>
                            </div>
                            <div class="px-5 py-2 py-lg-2">
                                <div class="row align-items-center">
                                    <div class="col-12 col-lg">
                                        <form method="POST" action="{{ route('reset.pass.newpass-form-store') }}">
                                            @csrf
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label>{{ __('New password') }}</label>
                                                            <div class="input-group ">
                                                                <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('Password') }}" name="password">
                                                                @if ($errors->has('password'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('password') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                        <label>{{ __('Confirm password') }}</label>
                                                            <div class="input-group ">
                                                                <input type="password" class="form-control" placeholder="{{ __('Confirm Password') }}" name="password_confirmation">

                                                                @if ($errors->has('password_confirmation'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group text-right">
                                                <button class="btn btn-danger btn-lg btn-block" type="submit">
                                                    {{ __('Change') }}
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
