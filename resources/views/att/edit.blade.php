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
<span class="text-muted fw-light">Absensi / Acara / </span>
@endsection


@section('content')
<div class="card">
    <div class="card-datatable table-responsive">
        <div class="card-body">
            <form action="" method="POST">
                @csrf
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="basicDate">Judul</label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror" name="judul"
                            placeholder="" value="{{ $data->title }}">
                        @error('judul')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="basicDate">Lokasi</label>
                        <input type="text" class="form-control @error('lokasi') is-invalid @enderror" name="lokasi"
                            placeholder="" value="{{ $data->location }}">
                        @error('lokasi')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="basicDate">Tanggal</label>
                        <input type="date" class="form-control @error('tanggal') is-invalid @enderror" name="tanggal"
                            placeholder="" value="{{ $data->date }}">
                        @error('tanggal')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="basicDate">Tenggat Absensi</label>
                        <input type="datetime-local" class="form-control @error('tenggat_absensi') is-invalid @enderror"
                            name="tenggat_absensi" placeholder="" value="{{ $data->expired }}">
                        @error('tenggat_absensi')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="basicDate">Pimpinan Rapat</label>
                        <input type="text" class="form-control @error('pimpinan_rapat') is-invalid @enderror"
                            name="pimpinan_rapat" placeholder="" value="{{ $data->host }}">
                        @error('pimpinan_rapat')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="basicDate">Peserta</label>
                        <input type="text" class="form-control @error('peserta') is-invalid @enderror" name="peserta"
                            placeholder="" value="{{ $data->participant }}">
                        @error('peserta')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="mt-2">
                        <button type="submit" name="ubah" class="btn btn-primary me-2">Simpan</button>
                        <a class="btn btn-outline-secondary" href="{{ route('att.index') }}">Kembali</a>
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
