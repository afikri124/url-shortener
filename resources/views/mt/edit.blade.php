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
<span class="text-muted fw-light">Absensi / Rapat / </span>
@endsection


@section('content')
<div class="card">
    <div class="card-datatable table-responsive">
        <div class="card-body">
            <form action="" method="POST">
                @csrf
                <div class="row">
                    <div class="mb-3 col-md-12">
                        <label class="form-label" for="basicDate">Judul</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" name="title"
                            placeholder="" value="{{ $data->title }}">
                        @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                    <label class="form-label" for="basicDate">Judul Tambahan</label>
                        <input type="text" class="form-control @error('sub_title') is-invalid @enderror" name="sub_title"
                            placeholder="" value="{{ $data->sub_title }}">
                        @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                    <label class="form-label" for="basicDate">Tanggal</label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" name="date"
                            placeholder="" value="{{ $data->date }}">
                        @error('date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                    <label class="form-label" for="basicDate">Lokasi</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" name="location"
                            placeholder="" value="{{ $data->location }}">
                        @error('location')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                    <label class="form-label" for="basicDate">Pimpinan Rapat</label>
                        <input type="text" class="form-control @error('host') is-invalid @enderror" name="host"
                            placeholder="" value="{{ $data->host }}">
                        @error('host')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                    <label class="form-label" for="basicDate">Peserta</label>
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
                        <select class="form-select select2 col-sm-12 @error('notulen') is-invalid @enderror" name="notulen">
                            <option value="" selected disabled>--Select Notulen--</option>
                            @foreach($user as $d)
                            <option value="{{ $d->username }}" {{ ($d->username==$data->notulen_username ? "selected": "") }}>
                            {{ $d->name }} - {{ $d->username }}</option>
                            @endforeach
                        </select>
                        @error('notulen')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="mt-2">
                        <button type="submit" name="ubah" class="btn btn-primary me-2">Simpan</button>
                        <a class="btn btn-outline-secondary" href="{{ route('mt.index') }}">Kembali</a>
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
