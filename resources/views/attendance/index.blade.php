@extends('layouts.authentication.master')
@section('title', 'Attendance')
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
                                <img src="{{asset('assets/img/jgu.png')}}" width="150">
                            </span>
                        </a>
                    </div>
                    <!-- /Logo -->
                    <p class="mb-4 text-center">Silahkan Isi Absensi Anda</p>
                    @if(session('msg'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        {{session('msg')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <form id="formAuthentication" class="mb-3" action="" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="nama"
                                name="name" value="{{ Auth::user()->name }}" placeholder="Masukkan Nama" autofocus
                                disabled  />
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" @if(Auth::user()->email != null) readonly @endif
                                value="{{ (old('email') == null ? 
                                    ( strstr(Auth::user()->email, Auth::user()->username) == false ? Auth::user()->email : '') 
                                    : old('email')) }}"
                                placeholder="Masukkan Email"  />
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="user" class="form-label">Username / NIK / Matrix</label>
                            <input type="username" class="form-control @error('username') is-invalid @enderror"
                                id="user" name="username" value="{{ Auth::user()->username  }}"
                                placeholder="Masukkan Username"  @if(Auth::user()->username != null) readonly @endif />
                            @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="user" class="form-label">Lokasi</label>
                            <input type="location" class="form-control @error('location') is-invalid @enderror"
                                id="user" name="location" value="{{ old('location')  }}"
                                placeholder="Masukkan Lokasi"/>
                            @error('location')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <hr>
                        <div class="mb-3 text-center">
                            <button class="btn btn-primary  w-100" type="submit" name="attendance"><i
                                    class="bx bx-log-in-circle me-2"></i>Absen Sekarang</button>
                        </div>
                        @error('msg')
                        <b class="text-danger text-center m-0">{!! $message !!}</b>
                        @enderror
                    </form>
                    <div class="mb-3 text-center">
                        <a class="btn btn-light w-100" href="{{ route('logout') }}" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                            <i class="bx bx-x-circle me-2"></i>Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
@endsection
