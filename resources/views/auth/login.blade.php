@extends('layouts.authentication.master')
@section('title', 'Login')
@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-2">
                        <a href="{{ route('index') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <img src="{{asset('assets/img/logo-sjgu.png')}}" width="150">
                            </span>
                        </a>
                    </div>
                    <br>                  
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
                    <small>
                    <center class="alert alert-secondary"><p><b><i class="text-danger"><i class="fa fa-info-circle"></i> Informasi !</i></b>
                        <br>Civitas Academica JGU masuk menggunakan <i>Single Sign-On SIAP</i> atau email JGU, sedangkan untuk tamu dapat masuk menggunakan google.</p>
                        {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
                    </center>
                    </small>
                    <div class="row d-flex justify-content-center">
                        <div class="divider my-2">
                            <div class="divider-text mb-2">Pilih Metode Masuk</div>
                            @error('msg')
                            <br><span class="text-danger text-center">{!! $message !!}</span>
                            @enderror
                        </div>
                        {{-- <div class="col-md-6 col-xs-12 mb-2">
                            <div class="btn-showcase">
                                <button class="btn btn-outline-dark btn-block w-100" onclick="Klas2Login()" title="Single Sign-On JGU (User Klas)">
                                    <img style="max-height: 15px;" src="{{asset('assets/img/favicon.png')}}">
                                    <span>SSO Klas2</span>
                                </button>
                            </div>
                        </div> --}}
                        <div class="col-md-6 col-xs-12 mb-2">
                            <div class="btn-showcase">
                                <a class="btn btn-outline-dark btn-block w-100" href="{{ url('login/siap') }}" title="Login dengan SSO Siakad (SIAP)">
                                    <img style="max-height: 15px; margin-right: 3px" src="{{asset('assets/img/icons/sevima.png')}}">
                                    <span>SSO SIAP</span>
                                    {{-- <img style="max-height: 20px;" src="{{asset('assets/img/icons/siakadcloud2018.png')}}"> --}}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-12 mb-2">
                            <div class="btn-showcase">
                                <a class="btn btn-outline-dark btn-block w-100" href="{{ url('login/google') }}" title="Login dengan (Email JGU / Gmail)">
                                    <img style="max-height: 15px; margin-right: 3px" src="{{asset('assets/img/icons/google.png')}}">
                                    <span>Google</span>
                                    {{-- <img style="max-height: 20px;" src="{{asset('assets/img/icons/google2.png')}}"> --}}
                                </a>
                            </div>
                        </div>
                    </div>
                    <br><small>
                    <center><i>Jika terdapat kendala masuk atau belum memiliki akun silakan menghubungi tim <a target="_blank" href="https://s.jgu.ac.id/m/itic">ITIC</a></i></center></small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php

if(!session()->has('url.intended'))
{
    if(((url()->previous() != route('index')) || (url()->previous() != route('index')."/")) && isset($_GET['redirect_to'])){
        $linkcheck = parse_url(url()->previous());
        if($linkcheck['host'] == "s.jgu.ac.id" || $linkcheck['host'] == "127.0.0.1"){
            session(['url.intended' => $_GET['redirect_to']]);
        }
    }
} else if (session()->has('url.intended') && !isset($_GET['redirect_to'])) {
    $url = route('login')."?redirect_to=".session('url.intended');
    $linkcheck = parse_url(url()->previous());
    if($linkcheck['host'] == "s.jgu.ac.id" || $linkcheck['host'] == "127.0.0.1"){
        header("Location: $url"); 
        exit();
    }
} else {
    echo "<center><small style='color: #bcbcbc36'>Redirect to : ".session('url.intended')."</small></center>";
}


$login_name = env('APP_NAME');
$api_key = Crypt::encrypt(env('APP_KEY').gmdate('Y/m/d'));
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
        window.location.href = "{!!$link!!}";
    }
</script>
@endsection
