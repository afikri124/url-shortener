@extends('layouts.master')
@section('title', 'Acara')

@section('breadcrumb-items')
<span class="text-muted fw-light">Absensi /</span>
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
    table.dataTable tbody td {
        vertical-align: middle;
    }
    table.dataTable td:nth-child(2) {
        max-width: 200px;
    }

    table.dataTable td:nth-child(3) {
        max-width: 80px;
    }

    table.dataTable td:nth-child(4) {
        max-width: 50px;
    }

    table.dataTable td:nth-child(5) {
        max-width: 50px;
    }

    table.dataTable td:nth-child(6) {
        max-width: 50px;
    }

    table.dataTable td:nth-child(7) {
        max-width: 50px;
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
            <div class="row">
                <div class="col-12 pt-3 pt-md-0">
                    <div class="col-12">
                        <div class="row">
                            <div class="offset-md-9 col-md-3 text-md-end text-center pt-3 pt-md-0">
                                <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas"
                                    data-bs-target="#newrecord" aria-controls="offcanvasEnd" tabindex="0"
                                    aria-controls="DataTables_Table_0" type="button"><span><i
                                            class="bx bx-plus me-sm-2"></i>
                                        <span>Buat Baru</span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="offcanvas offcanvas-end @if($errors->all()) show @endif" tabindex="-1" id="newrecord"
                aria-labelledby="offcanvasEndLabel">
                <div class="offcanvas-header">
                    <h5 id="offcanvasEndLabel" class="offcanvas-title">Buat Absensi</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body my-auto mx-0 flex-grow-1">
                    <form class="add-new-record pt-0 row g-2 fv-plugins-bootstrap5 fv-plugins-framework"
                        enctype="multipart/form-data" id="form-add-new-record" method="POST" action="">
                        @csrf
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Judul</label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="text" class="form-control @error('judul') is-invalid @enderror" name="judul"
                                    placeholder="Judul Acara/Kegiatan" value="{{ old('judul') }}">
                                @error('judul')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Tanggal</label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror" name="tanggal"
                                    placeholder="" value="{{ old('tanggal') }}">
                                @error('tanggal')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Tenggat Absensi</label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="datetime-local" class="form-control @error('tenggat_absensi') is-invalid @enderror" name="tenggat_absensi"
                                    placeholder="" value="{{ old('tenggat_absensi') }}">
                                @error('tenggat_absensi')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Lokasi</label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="text" class="form-control @error('lokasi') is-invalid @enderror" name="lokasi"
                                    placeholder="Tempat/Ruangan" value="{{ old('lokasi') }}">
                                @error('lokasi')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Pemimpin </label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="text" class="form-control @error('pemimpin_rapat') is-invalid @enderror" name="pemimpin_rapat"
                                    placeholder="Nama Pemimpin dengan Gelar" value="{{ old('pemimpin_rapat') }}">
                                @error('pemimpin_rapat')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Peserta</label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="text" class="form-control @error('peserta') is-invalid @enderror" name="peserta"
                                    placeholder="Departemen Peserta" value="{{ old('peserta') }}">
                                @error('peserta')
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
                    <th width="20px" data-priority="1">No</th>
                    <th data-priority="2">Judul</th>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                    <th>Pemimpin</th>
                    <th>Peserta</th>
                    <th width="85px" data-priority="3">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('assets/vendor/libs/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables.responsive.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/responsive.bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables.checkboxes.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables-buttons.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/buttons.bootstrap5.js')}}"></script>
<script src="{{asset('assets/js/sweetalert.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
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
    setTimeout(function () {
        (function ($) {
            "use strict";
            $(".select2-modal").select2({
                dropdownParent: $('#newrecord'),
                allowClear: true,
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
                searchPlaceholder: 'Cari..',
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            ajax: {
                url: "{{ route('att.data') }}",
                data: function (d) {
                    d.select_dosen = $('#select_dosen').val(),
                        d.select_kategori = $('#select_kategori').val(),
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
                        return `<a href="{{ url('ATT/list/` +
                                row.idd + `') }}"><span title='` + row.title + `'>` + row.title + `</span></a>`;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return row.date;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                      return "<span title='" + row.location + "'>" + row.location + "</span>";
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return "<span title='" + row.host + "'>" + row.host + "</span>";
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return "<span title='" + row.participant + "'>" + row.participant + "</span>";
                    },
                },
                {
                    render: function (data, type, row, meta) {
                            return `<a class="text-info" target="_blank" title="Print QR" href="{{ url('ATT/print/` +
                                row.idd + `') }}"><i class="bx bxs-printer"></i></a> <a class="text-success" title="Edit" href="{{ url('ATT/edit/` +
                                row.idd + `') }}"><i class="bx bxs-edit"></i></a>
                                <a class="text-danger" title="Hapus" style="cursor:pointer" onclick="DeleteId(` + row.id +
                                `)" ><i class="bx bx-trash"></i></a> `;
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
                        url: "{{ route('att.delete') }}",
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
