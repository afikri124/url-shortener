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
        <h3 class="text-light">Silahkan gunakan akun berikut ini untuk terhubung ke Portal Wifi Jakarta Global University</h3><br>
        <div class="mb-3">
            <label for="user" class="form-label text-light">Username</label>
            <input type="text" class="form-control text-center" value="{{$username}}"/>
        </div>
        <div class="mb-3">
            <label for="user" class="form-label text-light">Password</label>
            <input type="text" class="form-control  text-center" value="{{$password}}"/>
        </div>
        <br>
        @if(Auth::user()->hasRole('ST'))
        @if($password != 'SILAHKAN HUBUNGI ITIC JGU')
        <blockquote class="text-danger"><b>Peringatan!</b><br>Jangan beritahukan <i>username</i> dan 
            <i>password</i> ini kepada siapapun, karena akan mempengaruhi kecepatan internet Anda.</blockquote>
        @else
        @endif
        @else
        <blockquote class="text-danger mt-3" style="font-size: 10pt"><b>Maaf!</b><br>Anda hanya mendapatkan akses terbatas, info lebih lanjut silahkan hubungi ITIC JGU.</blockquote>
        @endif
        @else
        <blockquote class="text-danger mt-3" style="font-size: 15pt"><b>Maaf!</b><br>Anda tidak memiliki akses wifi Kampus, silahkan <i>login</i> sebagai <i>GUEST</i>.</blockquote>
        @endif
    </div>
</div>
@endsection

@section('script')
@endsection
