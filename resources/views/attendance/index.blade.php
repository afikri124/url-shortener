@extends('layouts.authentication.master')
@section('title', 'Absensi')
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
                    @if(session('msg'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        {{session('msg')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @else
                    @if($check !=null)
                    <div class="alert alert-success alert-dismissible" role="alert">
                        Anda sudah melakukan absen pada <br>{{ \Carbon\Carbon::parse($check->created_at)->translatedFormat("l, d F Y H:i");}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @else
                    <p class="mb-4 text-center">Silahkan Isi Absensi Anda</p>
                    @endif
                    @endif
                    <form id="formAuthentication" class="mb-3" action="" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Legkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                name="nama" value="{{ Auth::user()->name_with_title }}" disabled />
                            @if(Auth::user()->hasRole('GS'))
                            <small class="text-mute">
                                <strong class="text-danger">*</strong> jika Anda ingin memperbarui nama Anda
                                <a href="{{ route('user.edit') }}" target="_blank"><strong>klik disini</strong></a>
                            </small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" @if(Auth::user()->email != null) readonly @endif
                            value="{{ (old('email') == null ? Auth::user()->email : old('email')) }}" />
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 @if(Auth::user()->hasRole('GS')) d-none @endif">
                            <label for="user" class="form-label">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="user"
                                name="username" placeholder="NIK / NIM / Matrix ID"
                                value="{{ (old('username') == null ? Auth::user()->username : old('username')) }}"
                                @if(Auth::user()->username != null) readonly @endif />
                            @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan"
                                name="jabatan" value="{{ (old('jabatan') == null ? Auth::user()->job : old('jabatan')) }}" @if($check !=null)
                                readonly @endif />
                            @error('jabatan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Judul @if($data->type == "M") Rapat @else Acara @endif </label>
                            <input type="text" class="form-control" name="judul" value="{{ $data->title }} {{ $data->sub_title }}"
                                readonly />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hari/Tanggal</label>
                            <input type="text" class="form-control" name="tanggal"
                                value='{{ \Carbon\Carbon::parse($data->date)->translatedFormat("l, d F Y"); }}' readonly />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lokasi/Tempat</label>
                            <input type="text" class="form-control"
                                name="lokasi"
                                value="{{ $data->location }}" readonly/>
                        </div>
                        <hr>
                        <div class="mb-3 text-center">
                            <button class="btn btn-success w-100" type="submit" name="attendance" @if($check !=null)
                                disabled @endif><i class="bx bx-log-in-circle me-2"></i>Absen Sekarang</button>
                        </div>
                        <div class="mb-3 text-center">
                            <button class="btn btn-dark w-100" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                <i class="bx bx-log-out-circle me-2"></i>Keluar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
@endsection
