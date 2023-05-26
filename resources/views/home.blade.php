@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
<div class="row justify-content-center">
    <!-- <div class="col-md-12">
        <div class="card">
            <div class="card-header">{{ __('Dashboard') }}</div>

            <div class="card-body">
                @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
                @endif

                {{ __('You are logged in!') }}
            </div>
        </div>
    </div> -->
    <div class="col-12">
        <div class="app-brand justify-content-center mb-4">
            <!-- <a href="https://jgu.ac.id/" target="_blank" class="app-brand-link gap-2"> -->
                <span class="">
                    <img src="{{asset('assets/img/girl-unlock-password-light.png')}}" style="width: 100%; max-width:700px;">
                </span>
            <!-- </a> -->
        </div>
    </div>
</div>

@endsection
