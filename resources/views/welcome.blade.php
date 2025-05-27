@extends('layouts.authentication.master')
@section('title', 'Welcome!')
@section('content')
<!-- Content -->

<div class="authentication-wrapper authentication-cover">
    <div class="authentication-inner row m-0">
        <!-- /Left Text -->
        <div class=" col-lg-7 align-items-center p-5">
            <div class="w-100 d-flex justify-content-center">
                <img src="{{asset('assets/img/girl-unlock-password-light.png')}}" class="img-fluid" alt="Login image"
                    width="700" data-app-dark-img="{{asset('assets/img/girl-unlock-password-light.png')}}"
                    data-app-light-img="{{asset('assets/img/girl-unlock-password-light.png')}}">
            </div>
        </div>
        <!-- /Left Text -->
        <!-- Login -->
        <div class="d-flex col-lg-5 align-items-center authentication-bg p-sm-5 p-4">
            <div class="w-px-400 mx-auto text-center justify-content-center">
                <!-- Logo -->
                <div class="app-brand justify-content-center mb-4">
                    <a href="https://jgu.ac.id/" target="_blank" class="app-brand-link gap-2">
                        <span class="app-brand-logo demo">
                            <img src="{{asset('assets/img/jgu.png')}}" width="150">
                        </span>
                    </a>
                </div>
                <!-- /Logo -->
                <i class="mb-2">Buat tautan panjang Anda lebih pendek dengan menggunakan
                    domain resmi <strong>s.jgu.ac.id</strong>,<br>
                    <strong>QR-Code</strong> resmi dari JGU,<br>
                    dan Anda juga bisa membuat
                    <strong>absensi</strong> acara/rapat.</i><br>
                <br>
                @if (Route::has('login'))
                @auth
                <a href="{{ route('home') }}" class="btn btn-danger text-white text-center w-50"><i
                        class="bx bx-home me-2"></i>Dashboard</a>
                @else
                <!-- <a href="{{ route('login') }}" class="btn btn-dark text-white text-center w-50"><i
                        class="bx bx-log-in-circle me-2"></i>Masuk</a> -->
                <div class="row d-flex justify-content-center">
                    <div class="my-2">
                        <div class="divider">
                            <div class="divider-text"><a href="/login" class="text-dark">Pilih Metode Masuk</a></div>
                        </div>
                        @error('msg')
                        <br><span class="text-danger text-center">{!! $message !!}</span>
                        @enderror
                    </div>
                    {{-- <div class="col-md-6 col-xs-12 mb-2">
                                <div class="btn-showcase">
                                    <button class="btn btn-outline-dark btn-block w-100" onclick="Klas2Login()"
                                        title="Single Sign-On JGU">
                                        <img style="max-height: 20px;" src="{{asset('assets/img/favicon.png')}}">
                            <span>SSO Klas</span>
                            </button>
                        </div>
                    </div> --}}
                    <div class="col-md-6 col-xs-12 mb-2">
                        <div class="btn-showcase">
                            <a class="btn btn-outline-dark btn-block w-100" href="{{ url('login/siap') }}"
                                title="Log in with SSO SIAP">
                                <img style="max-height: 15px; margin-right: 3px" src="{{asset('assets/img/icons/sevima.png')}}">
                                <span>SSO SIAP</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 mb-2">
                        <div class="btn-showcase">
                            <a class="btn btn-outline-dark btn-block w-100" href="{{ url('login/google') }}"
                                title="Log in with Email">
                                <img style="max-height: 15px; margin-right: 3px" src="{{asset('assets/img/icons/google.png')}}">
                                <span>Google</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endauth
            @endif
            <div class="divider mt-3">
                <div class="divider-text">© {{ (date('Y')=="2022"?date('Y'):"2022-".date('Y')) }}</div>
            </div>
            <div class="footer">
                <span class="mr-2">Dikembangkan oleh </span>
                <a href="https://itic.jgu.ac.id/" target="_blank" class="footer-link fw-bolder ml-2">ITIC JGU</a>
            </div>
            <small class="ml-4 text-center text-sm text-light sm:text-right sm:ml-0">
                v{{ Illuminate\Foundation\Application::VERSION }} (v{{ PHP_VERSION }})
            </small>
        </div>
    </div>
    <!-- /Login -->

    </div>
</div>

<!-- / Content -->
@endsection

@php

$login_name = env('APP_NAME');
$api_key = Crypt::encrypt(env('APP_KEY').gmdate('Y/m/d'));
Session::put('klas2_api_key', $api_key);
$callback_url = route('sso_klas2');
$token = md5($api_key.$callback_url);
$url = "http://klas2id.msu.edu.my/sso/";
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
