@extends('layouts.master')
@section('title', 'Rekap Jam Kerja')

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

    table.dataTable td:nth-child(3) {
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
                                <select id="select_user" class="select2 form-select" data-placeholder="Pilih Akun">
                                    <option value="">Pilih Akun</option>
                                    @foreach($user as $d)
                                    <option value="{{ $d->username }}">{{ ($d->user==null ? "[".$d->name."]" : $d->user->name )}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="offset-md-6 col-md-3 text-md-end text-center pt-3 pt-md-0">
                                <button class="btn btn-outline-dark" type="button" onclick="SyncAtt()">
                                    <span><i class="bx bx-sync me-sm-2"></i>
                                        Sinkron</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-hover table-sm text-md-center" id="datatable" width="100%">
            <thead>
                <tr>
                    <th width="30px" data-priority="1">No</th>
                    <th data-priority="2">Nama<br><code>userid mesin</code></th>
                    <th width="60px">Tanggal</th>
                    <th width="60px">Masuk</th>
                    <th width="60px">Keluar</th>
                    <th width="60px">Telat</th>
                    <th width="80px">Plg Cepat</th>
                    <th width="60px">Lembur</th>
                    <th width="80px" data-priority="3">Jam Hadir</th>
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
                searchPlaceholder: 'Cari username..',
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            ajax: {
                url: "{{ route('WHR.data') }}",
                data: function (d) {
                    d.select_user = $('#select_user').val(),
                        d.search = $('input[type="search"]').val()
                },
            },
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }],
            columns: [
                {
                    render: function (data, type, row, meta) {
                        var no = (meta.row + meta.settings._iDisplayStart + 1);
                        return no;
                    },
                    className: "text-center"
                },
                {
                    render: function (data, type, row, meta) {
                        var html = `<code>` + row.username + `</code>`;
                        if(row.user != null){
                            html = `<a class="text-primary" title="` + row.user.name +
                                `" href="{{ url('profile/` + row.userid + `') }}">` + row.user
                                .name + `</a><br>` + html;
                        } else {
                            html = `<small title='Nama di Mesin'>[` + row.name + `]</small><br>` + html;
                        }
                        return html;
                    }, className: "text-start"
                },
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    render: function (data, type, row, meta) {
                        if(row.masuk != null){
                            return moment(row.masuk).format('H:mm');
                        }
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        if(row.keluar != null){
                            return moment(row.keluar).format('H:mm');
                        }
                    },
                },
                {
                    data: 'telat',
                    name: 'telat'
                },
                {
                    data: 'cepat',
                    name: 'cepat'
                },
                {
                    data: 'lembur',
                    name: 'lembur'
                },{
                    data: 'total_jam',
                    name: 'total_jam'
                },
                // {
                //     render: function (data, type, row, meta) {
                //         // var html =
                //         //     `<a class=" text-success" title="Edit" href="{{ url('setting/account_att/edit/` +
                //         //     row.idd + `') }}"><i class="bx bxs-edit"></i></a>`;
                //         // return html;
                //     },
                //     className: "text-center"
                // }
            ]
        });
        $('#select_user').change(function () {
            table.draw();
        });
    });

    function SyncAtt() {
        swal({
                title: "Konfirmasi Sinkronisasi Data",
                text: "Sistem akan mengambil data Absensi dari mesin absen",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((x) => {
                if (x) {
                    $.ajax({
                        url: "{{ route('WHR.sync') }}",
                        type: "GET",
                        data: {
                            "_token": $("meta[name='csrf-token']").attr("content"),
                        },
                        beforeSend: function (xhr) {
                            document.getElementById('loadingSync').style.display = 'block';
                            document.getElementById('loadingSyncText').innerHTML = 'Menyinkronkan data dari mesin Absensi..'
                        },
                        complete: function () {
                            document.getElementById('loadingSync').style.display = 'none';
                            document.getElementById('loadingSyncText').innerHTML = ''
                        },
                        success: function (data) {
                            if(data['success']){
                                $('#datatable').DataTable().ajax.reload();
                                swal(data['total'] + " data tersinkron..", {
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
