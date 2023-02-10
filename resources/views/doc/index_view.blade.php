@extends('layouts.master')
@section('title', 'Unggah Bukti')

@section('breadcrumb-items')
<span class="text-muted fw-light">Dokumen /</span>
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('style')
<style>
    td {
        vertical-align: top;
        word-wrap: break-word;
    }

</style>
@endsection

@section('content')
@foreach ($errors->all() as $error)
<div class="alert alert-danger alert-dismissible" role="alert">
    {{ $error }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endforeach

<div class="row invoice-preview">
    <!-- Details Data -->
    <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
        <div class="card invoice-preview-card">
            <hr class="my-0">
            <div class="card-body">
                <div class="row p-0">
                    <div class="">
                        <h6 class="pb-2">{{ $data->name }}</strong>
                        </h6>
                        <table class="" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td class="pe-3 text-muted w-30" style="width: 130px;">Batas Waktu</td>
                                    <td class="w-70">{{$data->deadline}}</td>
                                </tr>
                                <tr>
                                    <td class="pe-3 text-muted w-30">Kategori</td>
                                    <td class="w-70">{{$data->category->name}}</td>
                                </tr>
                                <tr>
                                    <td class="pe-3 text-muted w-30">Status</td>
                                    <td class="w-70 text-{{$data->status->color}}">{{$data->status->name}}</td>
                                </tr>
                                @if($data->remark != null)
                                <tr>
                                    <td class="pe-3 text-muted w-30">Catatan</td>
                                    <td class="w-70">{{$data->remark}}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="pe-3 text-muted w-30"></td>
                                    <td class="w-70" style="max-width: 110px;">
                                        <a href="{{ $data->doc_path }}" class="btn btn-info d-grid w-100 my-3" target="_blank">
                                            <span class="d-flex align-items-center justify-content-center text-nowrap">
                                                <i class="bx bx-upload bx-xs me-3"></i>Unggah Bukti Disini
                                            </span>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card invoice-preview-card mt-4">
            <hr class="my-0">
            <div class="card-body">
                <div class="row p-0">
                    <div class="">
                        <h6 class="pb-2">Riwayat Perubahan Status</strong>
                        </h6>
                        {!! $data->histories !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Details Data -->

    <!-- Actions -->
    <div class="col-xl-3 col-md-4 col-12 invoice-actions">
        <div class="card">
            <div class="card-body text-center">
                <button class="btn btn-success d-grid w-100 mb-3" data-bs-toggle="offcanvas"
                    data-bs-target="#modalUnggah" @if($data->status_id == "S2" || $data->status_id == "S4") disabled
                    @endif >
                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i
                            class="bx bx-check bx-xs me-3"></i>Sudah Unggah</span>
                </button>
                <button class="btn btn-danger d-grid w-100 mb-3" data-bs-toggle="offcanvas" data-bs-target="#modalBatalkan"
                    @if($data->status_id != "S2") disabled @endif>
                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i
                            class="bx bx-x bx-xs me-3"></i>Batalkan</span>
                </button>
                <a href="{{ route('DOC.index') }}" class="btn btn-outline-secondary"><i
                        class="bx bx-chevron-left me-sm-2"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /Actions -->
</div>

<!-- Offcanvas -->
<!--Diterima Sidebar -->
@if($data->status_id != "S2" || $data->status_id == "S4")
<div class="offcanvas offcanvas-end" id="modalUnggah" aria-hidden="true">
    <div class="offcanvas-header mb-3">
        <h5 class="offcanvas-title">Konfirmasi Unggah Bukti</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form method="POST" action="">
            @csrf
            <div class="mb-3">
                <label class="form-label">Catatan</label>
                <textarea class="form-control" name="catatan" cols="3" rows="8" placeholder="Boleh dikosongkan.."></textarea>
            </div>
            <div class="mb-3 d-flex flex-wrap">
                <input type="hidden" name="action" value="unggah">
                <button type="submit" class="btn btn-success me-3" data-bs-dismiss="offcanvas">Konfirmasi</button>
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Batal</button>
            </div>
        </form>
    </div>
</div>
@endif
<!-- /Sidebar -->

<!-- Ditolak Sidebar -->
<div class="offcanvas offcanvas-end" id="modalBatalkan" aria-hidden="true">
    <div class="offcanvas-header mb-3">
        <h5 class="offcanvas-title">Batalkan Status</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form method="POST" action="">
            @csrf
            <div class="mb-3">
                <label class="form-label">Catatan <i class="text-danger">*</i></label>
                <textarea class="form-control @error('catatan') is-invalid @enderror" name="catatan" cols="3"
                    rows="8"></textarea>
                @error('catatan')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="mb-3 d-flex flex-wrap">
                <input type="hidden" name="action" value="batalkan">
                <button type="submit" class="btn btn-danger me-3" data-bs-dismiss="offcanvas">Submit</button>
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Batal</button>
            </div>
        </form>
    </div>
</div>
<!-- /Sidebar -->

<!-- /Offcanvas -->

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
