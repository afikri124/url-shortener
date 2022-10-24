@extends('layouts.master')
@section('title', 'Edit Profile')
@section('breadcrumb-items')
<span class="text-muted fw-light">User / </span>
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('style')
<style>

</style>
@endsection

@section('content')

@include('user._header')
<!-- User Profile Content -->
<div class="row">
    <div class="col-md-12">
        @if(session('msg'))
        <div class="alert alert-primary alert-dismissible" role="alert">
            {{session('msg')}}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="card mb-4">
            <h5 class="card-header">Edit Profile</h5>
            <!-- Account -->
            <hr class="my-0">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="row">
                        @csrf
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                value="{{ Auth::user()->name }}" placeholder="Full Name" autofocus />
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                name="username" value="{{ Auth::user()->username }}" placeholder="NIK/NIM"
                                @if(Auth::user()->username != null ) readonly title="Please contact Administrator" @endif />
                            @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Front Title (Prefix)</label>
                            <input type="text" class="form-control @error('front_title') is-invalid @enderror"
                                name="front_title" value="{{ Auth::user()->front_title }}" />
                            @error('front_title')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Back Title (Academic Degree)</label>
                            <input type="text" class="form-control @error('back_title') is-invalid @enderror"
                                name="back_title" value="{{ Auth::user()->back_title }}" />
                            @error('back_title')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Job</label>
                            <input type="text" class="form-control @error('job') is-invalid @enderror" name="job"
                                value="{{ (old('job') == null ? Auth::user()->job : old('job')) }}" />
                            @error('job')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email"
                                value="{{ (old('email') == null ? Auth::user()->email : old('email')) }}" />
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Gender</label>
                            <select class="select2 form-select col-sm-12 @error('gender') is-invalid @enderror"
                                name="gender">
                                <option value="" {{ (Auth::user()->gender == null ? 'selected' : '') }} disabled>
                                    Select</option>
                                @foreach($gender as $g)
                                <option {{ ((Auth::user()->gender == $g) ? 'selected' : ''); }}> {{$g}}</option>
                                @endforeach

                            </select>
                            @error('gender')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Role Access</label>
                            <select class="select2 form-select" multiple="multiple" name="roles[]" id="select2Dark"
                                disabled>
                                @foreach($roles as $role)
                                <option value="{{$role->id}}" {{ Auth::user()->hasRole($role->id) ? 'selected' : '' }}>
                                    {{$role->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Save</button>
                    </div>
                </form>
            </div>
            <!-- /Account -->
        </div>
    </div>
</div>

<!--/ User Profile Content -->
@endsection
@section('script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script>
    "use strict";
    $(function () {
        const e = $(".selectpicker"),
            t = $(".select2"),
            c = $(".select2-icons");

        function i(e) {
            return e.id ? "<i class='bx bxl-" + $(e.element).data("icon") + " me-2'></i>" + e.text : e.text
        }
        e.length && e.selectpicker(), t.length && t.each(function () {
            var e = $(this);
            e.wrap('<div class="position-relative"></div>').select2({
                placeholder: "Select value",
                dropdownParent: e.parent()
            })
        }), c.length && c.wrap('<div class="position-relative"></div>').select2({
            templateResult: i,
            templateSelection: i,
            escapeMarkup: function (e) {
                return e
            }
        })
    });

</script>
@endsection
