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
                    @if(session('msg'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        {{session('msg')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @else
                        @if($check !=null)
                        <div class="alert alert-success alert-dismissible" role="alert">
                            You have been absent on<br>{{ date('l, d F Y H:i', strtotime($check->created_at)) }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @else
                        <p class="mb-4 text-center">Please Fill Your Attendance</p>
                        @endif
                    @endif
                    <form id="formAuthentication" class="mb-3" action="" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="nama"
                                name="name" value="{{ Auth::user()->name_with_title }}" disabled />
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

                        @if(Auth::user()->hasRole('GS') || Auth::user()->job == null)
                        <div class="mb-3">
                            <label for="job" class="form-label">Job</label>
                            <input type="text" class="form-control @error('job') is-invalid @enderror" id="job"
                                name="job" value="{{ (old('job') == null ? Auth::user()->job : old('job')) }}" 
                                @if($check !=null) readonly @endif
                                />
                            @error('job')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">@if($data->type == "M") Meeting @else Event @endif Title</label>
                            <input type="text" class="form-control"
                                name="activity" value="{{ $data->title }}" readonly/>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="text" class="form-control" name="date"
                                value="{{ date('l, d F Y', strtotime($data->date)) }}" readonly />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror"
                                name="location"
                                value="{{ (old('location') == null ? $data->location : old('location')) }}"
                                @if($data->location != null) readonly @endif/>
                        </div>
                        <hr>
                        <div class="mb-3 text-center">
                            <button class="btn btn-success w-100" type="submit" name="attendance" @if($check !=null)
                                disabled @endif><i class="bx bx-log-in-circle me-2"></i>Attend Now</button>
                        </div>
                        <div class="mb-3 text-center">
                            <button class="btn btn-dark w-100" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                <i class="bx bx-x-circle me-2"></i>Logout
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
