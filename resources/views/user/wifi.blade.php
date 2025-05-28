@extends('layouts.master')
@section('title', 'Akun Portal Wifi')

@section('breadcrumb-items')
<!-- <span class="text-muted fw-light"> /</span> -->
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/sweetalert2.css')}}">
@endsection

@section('style')
@endsection

@section('content')
<div class="text-center mt-5 d-flex justify-content-center">
    <div class="mb-3 col-md-4">
        <i class="bx bx-wifi text-success" style="font-size: 50pt"></i>
        @if(Auth::user()->hasRole('ST') || Auth::user()->hasRole('SD') || Auth::user()->hasRole('GS'))
        <h4 class="text-light">Silahkan menggunakan akun berikut ini untuk terhubung ke Portal Wifi <b>Jakarta Global
                University</b></h4>
        <br>

        <div class="mb-3">
            <label for="user" class="form-label text-light">Username</label>
            <input type="text" class="form-control text-center" value="{{$username}}" disabled />
        </div>
        <div class="mb-3">
            <label for="user" class="form-label text-light">Password</label>
            <input type="text" class="form-control  text-center" value="{{$password}}" disabled />
        </div>
        <div class="mb-3">
            <label for="user" class="form-label text-light">GRUP</label>
            <input type="text" class="form-control  text-center" value="{{$group}}" disabled />
        </div>
        <br>
        <div class="row d-flex justify-content-center">
            {{-- @if ($ip == "43.225.65.161" || $ip == "43.225.65.162" || $ip == "43.225.65.163") --}}
            <div class="col-md-6 col-xs-12 mb-2">
                <div class="btn-showcase">
                    <a class="btn btn-block w-100 btn-primary mb-3" target="_blank"
                        href="https://auth.jgu.ac.id/login?username={{$username}}&password={{$password}}">
                        <i class="bx bx-log-in-circle me-2"></i> Login Portal
                    </a>
                </div>
            </div>
            {{-- @endif --}}
            <div class="col-md-6 col-xs-12 mb-2">
                <div class="btn-showcase">
                    <a class="btn btn-block w-100 btn-secondary mb-3" href="{{ route('wifi.edit') }}">
                        <i class="bx bx-key me-2"></i> Ubah Sandi</a>
                </div>
            </div>
        </div>
        @if($password != 'SILAHKAN HUBUNGI ITIC JGU')
        <blockquote class="text-danger"><b>Peringatan!</b><br><small>Jangan memberikan <i>username</i> dan
                <i>password</i> ini kepada siapapun, kami membatasi jumlah login perangkat dan kecepatan internet
                berdasarkan grup yang Anda dapatkan.</small></blockquote>
        @endif
        @endif
        <small>{{ $agent_log }}</small>
    </div>
</div>
@endsection

@section('script')
@endsection
