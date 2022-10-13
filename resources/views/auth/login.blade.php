@extends('layouts.authentication.master')
@section('title', 'Login')
@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center">
                        <a href="{{ route('index') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <img src="{{asset('assets/img/logo-sjgu.png')}}" width="150">
                            </span>
                        </a>
                    </div>
                    <!-- /Logo -->
                    <!-- <h4 class="mb-2 text-center">Login</h4> -->
                    <!-- <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="user" class="form-label">Username atau Email</label>
                            <input type="username" class="form-control @error('username') is-invalid @enderror"
                                id="user" name="username" value="{{ old('username') }}"
                                placeholder="Masukkan Username atau Email" autofocus />
                            @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password" name="password">Password</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                        </div>
                        <div class="mb-3">
                            <div class=" d-flex justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Ingat Saya') }} </label>
                                </div>
                                <a href="{{ route('password.request') }}">
                                    <small>Lupa Password?</small>
                                </a>
                            </div>
                        </div>
                        <div class="mb-3 text-center">
                            <button class="btn btn-primary  w-100" type="submit" name="login"><i
                                    class="bx bx-log-in-circle me-2"></i>Log in</button>
                        </div>
                       
                    </form> -->
                    <div class="row">
                        <div class="divider my-2">
                            <div class="divider-text mb-2">Login Method</div>
                            @error('msg')
                            <br><span class="text-danger text-center">{!! $message !!}</span>
                            @enderror
                        </div>
                        <div class="col-6 mb-1">
                            <div class="btn-showcase">
                                <button class="btn btn-dark btn-block w-100" onclick="Klas2Login()" title="Single Sign-On JGU">
                                    <img style="max-height: 20px;" src="{{asset('assets/img/favicon.png')}}">
                                    <span>SSO JGU</span>
                                </button>
                            </div>
                        </div>
                        <div class="col-6 mb-1">
                            <div class="btn-showcase">
                                <a class="btn btn-dark btn-block w-100" href="{{ url('login/google') }}" title="Email JGU">
                                    <img style="max-height: 20px;"
                                        src="https://avatars.githubusercontent.com/u/19180220?s=200&v=4">
                                    <span>Google</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php

if(!session()->has('url.intended'))
{
    if(url()->previous() != route('index')."/"){
        session(['url.intended' => url()->previous()]);
    }
}

$login_name = "S.JGU";
$api_key = Crypt::encrypt("S.JGU".gmdate('Y/m/d'));
Session::put('klas2_api_key', $api_key);
$callback_url = route('sso_klas2');
$token = md5($api_key.$callback_url);
$url = "http://klas2.jgu.ac.id/sso/";
$link =
$url."?login_to=".route('login')."&login_name=$login_name&api_key=$api_key&callback_url=$callback_url&token=$token&ip=".$_SERVER['REMOTE_ADDR'];
@endphp
@section('script')
<script>
    function Klas2Login() {
        // alert("SSO");
        // window.open("{!!$link!!}", "LOGIN SSO JGU",
        //     "location=no, titlebar=no, toolbar=no, fullscreen='yes', resizable=no, scrollbars=yes");
        window.location.href = "{!!$link!!}";
    }

</script>
@endsection
