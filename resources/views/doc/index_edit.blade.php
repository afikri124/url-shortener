@extends('layouts.master')
@section('title', 'Unggah Bukti / Ubah')

@section('breadcrumb-items')
<span class="text-muted fw-light">Dokumen /</span>
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/sweetalert2.css')}}">
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

<div class="card mb-4">
    <form class="row p-3" method="POST" action="">
        @csrf
        <div class="col-md-5">
            <label class="form-label">Departemen <i class="text-danger">*</i></label>
            <div class="input-group mb-3">
                <select class="form-select select2 @error('departemen') is-invalid @enderror" name="departemen" data-placeholder="-- Pilih Departemen --">
                    <option value="">-- Pilih Departemen --</option>
                    @foreach($department as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
                @error('departemen')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-md-5">
            <label class="form-label">Penanggung Jawab <i class="text-danger">*</i></label>
            <div class="input-group mb-3">
            <select class="form-control form-select select2 @error('penanggung_jawab') is-invalid @enderror" name="penanggung_jawab" data-placeholder="-- Pilih PJ --">
                    <option value="">-- Pilih PJ --</option>
                    @foreach($user as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
                @error('penanggung_jawab')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-md-2 text-md-end text-center">
        <label class="form-label" style="color:#fff">.</label>
            <input type="hidden" name="action" value="tambah">
            <button class="btn btn-outline-primary w-100" type="submit" id="button-addon2">Tambah!</button>
        </div>
    </form>
</div>
<div class="card mb-4">
    <div class="card-datatable table-responsive">
        <table class="table table-hover table-sm" id="datatable" width="100%">
            <thead>
                <tr>
                    <th width="20px" data-priority="1">No</th>
                    <th data-priority="2">Departemen</th>
                    <th >penanggung jawab</th>
                    <th width="85px" data-priority="3">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>


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
                                    <td class="w-70">{{$data->status->name}}</td>
                                </tr>
                                <tr>
                                    <td class="pe-3 text-muted w-30"></td>
                                    <td class="w-70" style="max-width: 110px;">
                                        <a href="{{ $data->doc_path }}" class="btn btn-info d-grid w-100 my-3"
                                            target="_blank">
                                            <span class="d-flex align-items-center justify-content-center text-nowrap">
                                                <i class="bx bx-search-alt bx-xs me-3"></i>Lihat Bukti Disini
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
    @if(Auth::user()->hasRole('DS'))
    <div class="col-xl-3 col-md-4 col-12 invoice-actions">
        <div class="card">
            <div class="card-body text-center">
                <button class="btn btn-success d-grid w-100 mb-3" data-bs-toggle="offcanvas"
                    data-bs-target="#modalValidasi" @if($data->status_id != "S2" ) disabled
                    @endif >
                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i
                            class="bx bx-check bx-xs me-3"></i>Validasi</span>
                </button>
                <button class="btn btn-danger d-grid w-100 mb-3" data-bs-toggle="offcanvas"
                    data-bs-target="#modalRevisi" @if($data->status_id != "S2") disabled @endif>
                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i
                            class="bx bx-x bx-xs me-3"></i>Revisi</span>
                </button>
                <a href="{{ route('DOC.index') }}" class="btn btn-outline-secondary"><i
                        class="bx bx-chevron-left me-sm-2"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>
    @endif
    <!-- /Actions -->
</div>

<!-- Offcanvas -->
<!--Diterima Sidebar -->
@if($data->status_id != "S2")
<div class="offcanvas offcanvas-end" id="modalValidasi" aria-hidden="true">
    <div class="offcanvas-header mb-3">
        <h5 class="offcanvas-title">Validast Bukti</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form method="POST" action="">
            @csrf
            <div class="mb-3">
                <label class="form-label">Catatan</label>
                <textarea class="form-control" name="catatan" cols="3" rows="8"
                    placeholder="Boleh dikosongkan.."></textarea>
            </div>
            <div class="mb-3 d-flex flex-wrap">
                <input type="hidden" name="action" value="validasi">
                <button type="submit" class="btn btn-success me-3" data-bs-dismiss="offcanvas">Validasi</button>
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Batal</button>
            </div>
        </form>
    </div>
</div>
@endif
<!-- /Sidebar -->

<!-- Ditolak Sidebar -->
<div class="offcanvas offcanvas-end" id="modalRevisi" aria-hidden="true">
    <div class="offcanvas-header mb-3">
        <h5 class="offcanvas-title">Revisi</h5>
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
                <input type="hidden" name="action" value="revisi">
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
<script src="{{asset('assets/vendor/libs/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables.responsive.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/responsive.bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables.checkboxes.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables-buttons.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/js/sweetalert.min.js')}}"></script>
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
<script type="text/javascript">
       $(document).ready(function () {
        var table = $('#datatable').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ordering: false,
            language: {
                searchPlaceholder: 'Cari ..',
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            ajax: {
                url: "{{ route('DOC.index_edit_data',  ['id' => $data->id] ) }}",
                data: function (d) {
                    d.search = $('input[type="search"]').val()
                },
            },
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }],
            columns: [{
                    render: function (data, type, row, meta) {
                        var no = (meta.row + meta.settings._iDisplayStart + 1);
                        return no;
                    },
                    className: "text-center"
                },
                {
                    render: function (data, type, row, meta) {
                        if (row.department != null) {
                            return row.department.name;
                        }
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        if (row.user != null) {
                            return row.user.name;
                        }
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return `<a class="text-danger" title="Hapus" style="cursor:pointer" onclick="DeleteId(` + row
                            .id +
                            `)" ><i class="bx bx-trash"></i></a>`;
                    },
                    className: "text-md-center"
                }

            ]
        });
    });

    function DeleteId(id) {
        swal({
                title: "Apa kamu yakin?",
                text: "Setelah dihapus, data tidak dapat dipulihkan!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('DOC.index_edit_delete') }}",
                        type: "DELETE",
                        data: {
                            "id": id,
                            "_token": $("meta[name='csrf-token']").attr("content"),
                        },
                        success: function (data) {
                            if (data['success']) {
                                swal(data['message'], {
                                    icon: "success",
                                });
                                $('#datatable').DataTable().ajax.reload();
                            } else {
                                swal(data['message'], {
                                    icon: "error",
                                });
                            }
                        }
                    })
                }
            })
    }

</script>
@endsection
