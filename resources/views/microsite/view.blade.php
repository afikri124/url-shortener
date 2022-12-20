@extends('layouts.authentication.master')
@section('title', $data->title)
@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-2">
                        <a href="{{asset($data->avatar)}}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <img src="{{asset($data->avatar)}}"  class="rounded-circle"
                                style="object-fit: cover;" width="100px" height="100px">
                            </span>
                        </a>
                    </div>
                    <h4>{{$data->title}}</h4>
                    <span>{{$data->bio}}</span>
                    <br>                  
                    <div class="row p-2">
                        @foreach($links as $l)
                        <a href="{{$l->link}}" target="_blank" class="btn btn-block rounded-pill btn-outline-danger my-2">{{$l->title}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

