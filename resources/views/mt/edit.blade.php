@extends('layouts.master')
@section('title', 'Edit')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('style')
<style>
</style>
@endsection

@section('breadcrumb-items')
<span class="text-muted fw-light">Attendance / </span>
@endsection


@section('content')
<div class="card">
    <div class="card-datatable table-responsive">
        <div class="card-body">
            <form action="" method="POST">
                @csrf
                <div class="row">
                    <div class="mb-3 col-md-12">
                        <label class="form-label" for="basicDate">Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" name="title"
                            placeholder="" value="{{ $data->title }}">
                        @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                    <label class="form-label" for="basicDate">Sub Title</label>
                        <input type="text" class="form-control @error('sub_title') is-invalid @enderror" name="sub_title"
                            placeholder="" value="{{ $data->sub_title }}">
                        @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                    <label class="form-label" for="basicDate">Date</label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" name="date"
                            placeholder="" value="{{ $data->date }}">
                        @error('date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                    <label class="form-label" for="basicDate">Location</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" name="location"
                            placeholder="" value="{{ $data->location }}">
                        @error('location')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                    <label class="form-label" for="basicDate">Host</label>
                        <input type="text" class="form-control @error('host') is-invalid @enderror" name="host"
                            placeholder="" value="{{ $data->host }}">
                        @error('host')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                    <label class="form-label" for="basicDate">Participant</label>
                        <input type="text" class="form-control @error('participant') is-invalid @enderror" name="participant"
                            placeholder="" value="{{ $data->participant }}">
                        @error('participant')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                    <label class="form-label" for="basicDate">Notulen</label>
                        <input type="text" class="form-control @error('notulen') is-invalid @enderror" name="notulen"
                            placeholder="" value="{{ $data->notulen_username }}">
                        @error('notulen')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="mt-2">
                        <button type="submit" name="ubah" class="btn btn-primary me-2">Save</button>
                        <a class="btn btn-outline-secondary" href="{{ route('att.index') }}">Back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
@section('script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script>
    "use strict";
    setTimeout(function () {
        (function ($) {
            "use strict";
            $(".select2").select2({
                minimumResultsForSearch: 5
            });
        })(jQuery);
    }, 350);

</script>
@endsection
