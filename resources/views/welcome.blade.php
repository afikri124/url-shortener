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
                <i class="mb-2">Make your long links shorter by using<br>the official domain <strong>s.jgu.ac.id</strong>,<br> easily generate an JGU Official <strong>QRCode</strong>,<br>and you can also make<br>event/meeting <strong>attendance</strong>.</i><br><br>
                @if (Route::has('login'))
                @auth
                <a href="{{ route('home') }}" class="btn btn-danger text-white text-center w-50"><i
                        class="bx bx-home me-2"></i>Dashboard</a>
                @else
                <a href="{{ route('login') }}" class="btn btn-dark text-white text-center w-50"><i
                        class="bx bx-log-in-circle me-2"></i>Log in</a>
                @endauth
                @endif
                <br><br>
                <div class="divider mt-4">
                    <div class="divider-text">© 2022</div>
                </div>
                <div class="">
                    <span class="mr-2">Made by </span>
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
