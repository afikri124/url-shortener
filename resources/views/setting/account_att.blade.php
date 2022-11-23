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
<div class="col-sm-12 text-center justify-content-center mb-5" id="loadingSync" style="display: none;">
    <div class="spinner-border text-danger" role="status">
        <span class="visually-hidden">Tunggu...</span>
    </div>
    <br>
    Tunggu sebentar...<br>
    <span id="loadingSyncText"></span>
</div>

<div class="card">
    <div class="card-datatable table-responsive">
        <div class="card-header flex-column flex-md-row pb-0">
            <div class="row">
                <div class="col-12 pt-3 pt-md-0">
                    <div class="col-12">
                        <div class="row">
                            <div class=" col-md-3">
                                <select id="select_status" class="select2 form-select" data-placeholder="Status">
                                    <option value="">Status</option>
                                    @foreach($status as $d)
                                    <option value="{{ $d->id }}">{{ $d->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="offset-md-6 col-md-3 text-md-end text-center pt-3 pt-md-0">
                                <button class="btn btn-outline-dark" type="button" onclick="SyncUser()">
                                    <span><i class="bx bx-sync me-sm-2"></i>
                                        Sinkron</span>
                                </button>
                            </div>
                        </div>
                    </div>
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
                    <th width="60px">Password</th>
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
                    className: "text-center", name: "uid"
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
                    }, name: "name"
                },
                {
                    render: function (data, type, row, meta) {
                        var html = "<code title='NIK'>" + row.username + "</code>";
                        if (row.username_old != null) {
                            html += " <code title='Userid Mesin'>[" + row.username_old +
                                "]</code>";
                        }
                        return html;
                    }, name: "username"
                },
                {
                    data: 'role_name',
                    name: 'role'
                },
                {
                    data: 'password',
                    name: 'password'
                },
                {
                    data: 'cardno',
                    name: 'cardno'
                },
                {
                    render: function (data, type, row, meta) {
                        return row.status_name;
                    }, name: "status"
                },
                {
                    render: function (data, type, row, meta) {
                        var html =
                            `<a class=" text-success" title="Ubah" href="{{ url('setting/account_att/edit/` +
                            row.idd + `') }}"><i class="bx bxs-edit"></i></a>`;
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
                            document.getElementById('loadingSync').style.display = 'block';
                            document.getElementById('loadingSyncText').innerHTML =
                                'Menyinkronkan user dari mesin Absensi..';
                        },
                        complete: function () {
                            document.getElementById('loadingSync').style.display = 'none';
                            document.getElementById('loadingSyncText').innerHTML = '';
                            $('#datatable').DataTable().ajax.reload();
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

</script>
@endsection
