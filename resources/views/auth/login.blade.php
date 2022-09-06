@extends('layouts.authentication.master')
@section('title', 'Login')
@section('content')
<div class="card">
    <div class="card-body">
        <!-- /Logo -->
        <p class="mb-2 text-center">Selamat datang pada Sistem<br></p>
        <h5 class="mb-4 text-center">URL Shortener & QRCode Generator</h5>
        @error('msg')
        <b class="text-danger m-0">{{ $message }}</b>
        @enderror
        <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="user" class="form-label">Username / Email</label>
                <input type="username" class="form-control @error('username') is-invalid @enderror" name="username"
                    placeholder="Masukkan Username / Email" value="{{ old('username') }}" />
                @error('username')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                    <label class="form-label" for="password" name="password" required>Password</label>
                    <a href="{{ route('password.request') }}">
                        <small>Forgot Password?</small>
                    </a>
                </div>
                <div class="input-group input-group-merge">
                    <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
                        name="password"
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
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                        {{ old('remember') ? 'checked' : '' }} />
                    <label class="form-check-label" for="remember-me"> Ingat Saya </label>
                </div>
            </div>
            <div class="mb-3 text-center">
                <button class="btn btn-primary d-grid w-100" type="submit" name="login">Log in</button>
                <p class="mt-4">Atau Login Dengan</p>
            </div>
        </form>

        <div class="row">
            <div class="col-lg-6 col-sm-12 mb-4">
                <div class="btn-showcase">
                    <button class="btn btn-light btn-block w-100" onclick="Klas2Login()">
                        <img style="max-height: 20px;" src="{{asset('assets/img/favicon.png')}}">
                        <span>SSO Klas2</span>
                    </button>
                </div>
            </div>
            <div class="col-lg-6 col-sm-12 mb-4">
                <div class="btn-showcase">
                    <a class="btn btn-light btn-block w-100" href="{{ url('login/google') }}">
                        <img style="max-height: 20px;" src="https://avatars.githubusercontent.com/u/19180220?s=200&v=4">
                        <span>Google</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
$login_name = "URL Shortener and QRCode Generator";
$api_key = Crypt::encrypt("JGU".gmdate('Y/m/d'));
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
        window.open("{!!$link!!}", "LOGIN SSO JGU",
            "location=no, titlebar=no, toolbar=no, fullscreen='yes', resizable=no, scrollbars=yes");
    }

</script>
@endsection
