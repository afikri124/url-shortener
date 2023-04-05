@extends('layouts.master')
@section('title', 'Akun Portal Wifi')

@section('breadcrumb-items')
<span class="text-muted fw-light">Pengaturan /</span>
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/sweetalert2.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />
@endsection

@section('style')
<style>
    table.dataTable tbody td {
        vertical-align: middle;
    }

    table.dataTable td:nth-child(2) {
        max-width: 150px;
    }

    table.dataTable td:nth-child(4) {
        max-width: 90px;
    }

    table.dataTable td {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        word-wrap: break-word;
    }

</style>
@endsection


@section('content')
@if(session('msg'))
<div class="alert alert-primary alert-dismissible" role="alert">
    {{session('msg')}}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<div class="card">
    <div class="card-datatable table-responsive">
        <div class="card-header flex-column flex-md-row pb-0">
            {{-- <div class="row">
                <div class="col-12 pt-3 pt-md-0">
                    <div class="col-12">
                        <div class="row">
                            <div class="offset-md-9 col-md-3 text-md-end text-center pt-3 pt-md-0">
                                <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas"
                                    data-bs-target="#newrecord" aria-controls="offcanvasEnd" tabindex="0"
                                    aria-controls="DataTables_Table_0" type="button"><span><i
                                            class="bx bx-plus me-sm-2"></i>
                                        <span>Tambah</span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
            
            <div class="offcanvas offcanvas-end @if($errors->all()) show @endif" tabindex="-1" id="newrecord"
                aria-labelledby="offcanvasEndLabel">
                <div class="offcanvas-header">
                    <h5 id="offcanvasEndLabel" class="offcanvas-title">Tambah Pengguna Mesin Absensi</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body my-auto mx-0 flex-grow-1">
                    <form class="add-new-record pt-0 row g-2 fv-plugins-bootstrap5 fv-plugins-framework"
                        enctype="multipart/form-data" id="form-add-new-record" method="POST" action="">
                        @csrf
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">NIK / Username</label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="text" class="form-control @error('nik') is-invalid @enderror" name="nik"
                                    placeholder="Nomor Induk Karyawan" value="{{ old('nik') }}">
                                @error('nik')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Nama</label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama"
                                    placeholder="Nama (Maksimal 24 karakter)" value="{{ old('nama') }}" maxlength="24">
                                @error('nama')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mt-4">
                            <button type="submit" class="btn btn-primary data-submit me-sm-3 me-1">Submit</button>
                            <button type="reset" class="btn btn-outline-secondary"
                                data-bs-dismiss="offcanvas">Batal</button>
                        </div>
                        <div></div><input type="hidden">
                    </form>

                </div>
            </div>
        </div>
        <table class="table table-hover table-sm" id="datatable" width="100%">
            <thead>
                <tr>
                    <th width="10px" data-priority="1">No</th>
                    <th data-priority="2">Nama</th>
                    <th width="60px">Username</th>
                    <th width="150px">Password</th>
                    <th width="120px">Sudah Dilihat</th>
                    <th width="40px" data-priority="3">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables.responsive.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/responsive.bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables.checkboxes.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables-buttons.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/buttons.bootstrap5.js')}}"></script>
<script src="{{asset('assets/js/sweetalert.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script>
    "use strict";
    setTimeout(function () {
        (function ($) {
            "use strict";
            $(".select2").select2({
                allowClear: true,
                minimumResultsForSearch: 7
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
                searchPlaceholder: 'Cari..',
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            ajax: {
                url: "{{ route('setting_account_wifi_data') }}",
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
                    className: "text-center",
                    "orderable": false
                },
                {
                    render: function (data, type, row, meta) {
                        var html = row.name;
                        return html;
                    }
                },
                {
                    render: function (data, type, row, meta) {
                        var html = `<code>` + row.username + `</code>`;
                        return html;
                    },
                    className: "text-center",
                },
                {
                    render: function (data, type, row, meta) {
                        var html = `<code>` + row.password + `</code>`;
                        return html;
                    },
                    className: "text-center"
                },
                {
                    render: function (data, type, row, meta) {
                        if(row.is_seen){
                            return '<i class="bx bx-wifi text-success"></i> <small>' + moment(row.updated_at).format('d/m/y H:mm') + '</small>';
                        } else {
                            return '<i class="bx bx-low-vision text-light"></i>';
                        }
                    },
                    className: "text-center"
                },
                {
                    render: function (data, type, row, meta) {
                        var html = ``;
                        return html;
                    },
                    "orderable": false
                }
            ]
        });
    });

</script>
@endsection
