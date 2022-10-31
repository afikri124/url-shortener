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
<span class="text-muted fw-light">Penyingkat URL / </span>
@endsection


@section('content')
<div class="card">
    <div class="card-datatable table-responsive">
        <div class="card-body">
            <form  action="" method="POST">
                @csrf
                <div class="row">
                    <div class="mb-3 col-md-12">
                        <label for="penulis" class="form-label">Shortlink <i class="text-danger">*</i></label>
                        <input type="text" name="shortlink" value="{{ (old('shortlink') != null ? old('shortlink') : $data->shortlink) }}"
                            class="form-control @error('shortlink') is-invalid @enderror">
                        @error('shortlink')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="link" class="form-label">URL Panjang <i class="text-danger">*</i></label>
                        <input type="url" name="url" value="{{ (old('url') != null ? old('url') : $data->url) }}"
                            class="form-control @error('url') is-invalid @enderror" placeholder="http://..">
                        @error('url')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mt-2">
                        <button type="submit" name="ubah" class="btn btn-primary me-2">Simpan</button>
                        <a class="btn btn-outline-secondary" href="{{ route('url.index') }}">Kembali</a>
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
