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
        <h3 class="text-light">Silahkan menggunakan akun berikut ini untuk terhubung ke Portal Wifi <b>Jakarta Global University</b></h3>
        <br>
        
        <div class="mb-3">
            <label for="user" class="form-label text-light">Username</label>
            <input type="text" class="form-control text-center" value="{{$username}}" disabled/>
        </div>
        <div class="mb-3">
            <label for="user" class="form-label text-light">Password</label>
            <input type="text" class="form-control  text-center" value="{{$password}}" disabled/>
        </div>
        <div class="mb-3">
            <label for="user" class="form-label text-light">GRUP</label>
            <input type="text" class="form-control  text-center" value="{{$group}}" disabled/>
        </div>
        <br>
        <a class="btn btn-outline-secondary mb-3" target="_blank" href="https://auth.jgu.ac.id/login?username={{$username}}&password={{$password}}">Login Portal</a> 
        <a class="btn btn-outline-secondary mb-3" href="{{ route('wifi.edit') }}">Ubah Password</a><br><br>
        @if($password != 'SILAHKAN HUBUNGI ITIC JGU')
        <blockquote class="text-danger"><b>Peringatan!</b><br><small>Jangan memberikan <i>username</i> dan 
            <i>password</i> ini kepada siapapun, kami membatasi jumlah login perangkat dan kecepatan internet berdasarkan grup yang Anda dapatkan.</small></blockquote>
        @endif
        @endif
    </div>
</div>
@endsection

@section('script')
@endsection
