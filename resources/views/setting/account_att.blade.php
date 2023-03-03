@extends('layouts.master')
@section('title', 'Akun Mesin Absen')

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

    table.dataTable td:nth-child(3) {
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
            <div class="row">
                <div class="col-12 pt-3 pt-md-0">
                    <div class="col-12">
                        <div class="row">
                            <div class=" col-md-3">
                                <select id="select_group" class="select2 form-select" data-placeholder="Grup">
                                    <option value="">Grup</option>
                                    @foreach($group as $d)
                                    <option value="{{ $d->uid }}">{{ $d->title }} {{ ($d->desc==null?"":"(".$d->desc.")") }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-3">
                                <select id="select_status" class="select2 form-select" data-placeholder="Status">
                                    <option value="">Status</option>
                                    @foreach($status as $d)
                                    <option value="{{ $d->id }}">{{ $d->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="offset-md-3 col-md-3 text-md-end text-center pt-3 pt-md-0">
                                <button class="btn btn-outline-dark" type="button" onclick="SyncUser()">
                                    <span><i class="bx bx-sync me-sm-2"></i>
                                        Sinkron</span>
                                </button>
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
            </div>
            
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
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Grup</label>
                            <div class="input-group input-group-merge has-validation">
                                <select class="form-select @error('grup') is-invalid @enderror select2-modal" name="grup" data-placeholder="-- Pilih Grup --">
                                    <option value="">-- Pilih Grup --</option>
                                    @foreach($group as $d)
                                    <option value="{{ $d->uid }}">{{ $d->title }} {{ ($d->desc==null?"":"(".$d->desc.")") }}</option>
                                    @endforeach
                                </select>
                                @error('grup')
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
                    <th width="10px">UID</th>
                    <th data-priority="2">Nama [Nama di Mesin]</th>
                    <th>Username / [old]</th>
                    <th width="60px">Role</th>
                    <th width="60px">Group</th>
                    <th width="80px">No Kartu</th>
                    <th data-priority="4" width="50px">Status</th>
                    <th width="40px" data-priority="3">Aksi</th>
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
                url: "{{ route('setting_account_att_data') }}",
                data: function (d) {
                    d.select_status = $('#select_status').val(),
                    d.select_group = $('#select_group').val(),
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
                        var html = `<code>` + row.uid + `</code>`;
                        return html;
                    },
                    className: "text-center",
                    name: "uid"
                },
                {
                    render: function (data, type, row, meta) {
                        var html = "<small title='Nama di Mesin'>[" + row.name + "]</small>";
                        if (row.user != null) {
                            html = `<a class="text-primary" title="` + row.user.name +
                                `" href="{{ url('profile/` + row.userid + `') }}">` + row.user
                                .name + `</a><br>` + html;
                        }
                        return html;
                    },
                    name: "name"
                },
                {
                    render: function (data, type, row, meta) {
                        var html = "";
                        if (row.username != null) {
                            html = "<span title='NIK'>" + row.username + "</span>";
                        }
                        if (row.username_old != null) {
                            html += " <span title='Userid Mesin'>[" + row.username_old +
                                "]</span>";
                        }
                        if (row.status == 1) {
                            html = "<code>" + html + "</code>";
                        } else {
                            html = "<small>" + html + "</small>";
                        }
                        return html;
                    },
                    name: "username"
                },
                {
                    data: 'role_name',
                    name: 'role'
                },
                {
                    render: function (data, type, row, meta) {
                        if(row.group != null){
                            return row.group.title + (row.group.desc==null? "" : " (" + row.group.desc + ")");
                        }
                    },
                },
                {
                    data: 'cardno',
                    name: 'cardno'
                },
                {
                    render: function (data, type, row, meta) {
                        return row.status_name;
                    },
                    name: "status"
                },
                {
                    render: function (data, type, row, meta) {
                        var html =
                            `<a class=" text-success" title="Ubah" href="{{ url('setting/account_att/edit/` +
                            row.idd + `') }}"><i class="bx bxs-edit"></i></a>`;
                            html += ` <a class=" text-danger" title="Delete" style="cursor:pointer" onclick="DeleteId(` + row
                            .uid + `)" ><i class="bx bx-trash"></i></a>`;
                        return html;
                    },
                    className: "text-center",
                    "orderable": false
                }
            ]
        });
        $('#select_status').change(function () {
            table.draw();
        });
        $('#select_group').change(function () {
            table.draw();
        });
    });

    function SyncUser() {
        swal({
                title: "Konfirmasi Sinkronisasi Data",
                text: "Sistem akan mengambil data user dari mesin absen",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((x) => {
                if (x) {
                    $.ajax({
                        url: "{{ route('setting_account_att_sync') }}",
                        type: "GET",
                        data: {
                            "_token": $("meta[name='csrf-token']").attr("content"),
                        },
                        beforeSend: function (xhr) {
                            $.blockUI({
                                message: '<div class="spinner-border text-white" role="status">s.jgu</div><br>Menyinkronkan user dari mesin Absensi..',
                                css: {
                                    backgroundColor: "transparent",
                                    border: "0"
                                },
                                overlayCSS: {
                                    opacity: .5
                                }
                            })
                        },
                        complete: function () {
                            $('#datatable').DataTable().ajax.reload();
                            $.unblockUI();
                        },
                        success: function (data) {
                            // console.log(data);
                            if (data['success']) {
                                swal(data['total'] + " data tersinkron " +
                                    "(New:" + data['new'].length +
                                    ", Updated:" + data['updated'].length +
                                    ", Failed:" + data['failed'].length + ")", {
                                        icon: "success",
                                    });
                            } else {
                                swal("Terjadi Kesalahan! Hubungi Programmer..", {
                                    icon: "error",
                                });
                            }

                        }
                    })
                } else {
                    swal("Dibatalkan!", {
                        icon: "error",
                    });
                }
            })
    }

    function DeleteId(uid) {
        swal({
                title: "Apa kamu yakin? Data di mesin akan di hapus juga!",
                text: "Setelah dihapus, UID ( "+uid+" ) tidak dapat dipulihkan!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('setting_account_att_delete') }}",
                        type: "DELETE",
                        data: {
                            "uid": uid,
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
