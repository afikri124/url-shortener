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
<link rel="stylesheet" type="text/css"
    href="{{asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css')}}">
@endsection

@section('style')
<style>
    table.dataTable tbody td {
        vertical-align: middle;
    }

    table.dataTable td:nth-child(2) {
        max-width: 150px;
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
<div class="alert alert-secondary alert-dismissible" role="alert" id="lastupdate">
    Data terakhir disinkronkan {{ \Carbon\Carbon::parse($lastData->timestamp)->translatedFormat("l, d F Y H:i");}}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
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
                        <form method="POST" action="">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" id="select_range" name="range" class="form-control"
                                        placeholder="Pilih Tanggal" autocomplete="off" />
                                </div>
                                <div class=" col-md-3">
                                    <select id="select_user" class="select2 form-select" data-placeholder="Pilih Akun">
                                        <option value="">Pilih Akun</option>
                                        @foreach($user as $d)
                                        <option value="{{ ($d->username == null ? $d->username_old:$d->username) }}">
                                            {{ ($d->user==null ? "[".$d->name."]" : $d->user->name )}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 text-md-end text-center pt-3 pt-md-0">
                                    <button class="btn btn-outline-secondary" type="button" onclick="SyncAtt()">
                                        <span><i class="bx bx-sync me-sm-2"></i>
                                            Sinkron</span>
                                    </button>
                                    <button class="btn btn-primary" type="submit">
                                        <span><i class="bx bx-export me-sm-2"></i>
                                            Export</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-hover table-sm text-md-center" id="datatable" width="100%">
            <thead>
                <tr>
                    <th width="30px" data-priority="1">No</th>
                    <th data-priority="2">Nama<br><small>[Nama @ Mesin]</small></th>
                    <th>UserId @ Mesin</th>
                    <th width="90px" data-priority="4">Total Hari</th>
                    <th width="100px" data-priority="3">Total Jam</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/moment/id.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables.responsive.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/responsive.bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables.checkboxes.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables-buttons.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/buttons.bootstrap5.js')}}"></script>
<script src="{{asset('assets/js/sweetalert.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js')}}"></script>
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
            bFilter: false,
            language: {
                searchPlaceholder: 'Cari..',
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            lengthMenu: [
                [10, 50, 100, 250],
                [10, 50, 100, 250],
            ],
            ajax: {
                url: "{{ route('WHR.data') }}",
                data: function (d) {
                    d.select_user = $('#select_user').val(),
                        d.select_range = $('#select_range').val()
                    // d.search = $('input[type="search"]').val()
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
                        var html = `<small>[` + row.name + `]</small>`;
                        if (row.name2 != null) {
                            html = `<a class="text-primary" title="` + row.name2 +
                                `" href="{{ url('profile/` + row.userid + `') }}">` + row
                                .name2 + `</a><br>` + html;
                        }
                        return html;
                    },
                    className: "text-start"
                },
                {
                    render: function (data, type, row, meta) {
                        return `<code title="UserId di Mesin">[` + row.username + `]</code>`;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        if (row.hari != null) {
                            return row.hari;
                        }
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        if (row.total != null) {
                            return row.total;
                        }
                    },
                }
            ]
        });
        $('#select_user').change(function () {
            table.draw();
        });
        $('#select_range').change(function () {
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
                            document.getElementById('loadingSyncText').innerHTML =
                                'Menyinkronkan data dari mesin Absensi.';
                        },
                        complete: function () {
                            document.getElementById('loadingSync').style.display = 'none';
                            document.getElementById('loadingSyncText').innerHTML = '';
                            $('#datatable').DataTable().ajax.reload();
                            document.getElementById('lastupdate').style.display = 'none';

                        },
                        success: function (data) {
                            if (data['success']) {
                                swal(data['total'] + " data tersinkron..", {
                                    icon: "success",
                                });
                            } else {
                                swal("Sinkronisasi Gagal.. Total Data = " + data['total'], {
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
<script>
    //DateRange Picker
    (function ($) {
        $(function () {
            var start = moment().subtract(1, 'month').set("date", 20);
            var end = moment();

            function cb() {
                document.getElementById("select_range").value = null;
            }
            $('#select_range').daterangepicker({
                startDate: start,
                endDate: end,
                locale: 'id',
                showDropdowns: true,
                minYear: 2020,
                maxYear: parseInt(moment().format('YYYY'), 10),
                locale: {
                    format: 'YYYY-MM-DD'
                },
                ranges: {
                    'Hari ini': [moment(), moment()],
                    'Kemarin': [moment().subtract(1, 'day').startOf('day'), moment().subtract(1,
                        'day').endOf('day')],
                    'Minggu ini': [moment().startOf('week'), moment().endOf('week')],
                    'Minggu lalu': [moment().subtract(1, 'week').startOf('week'), moment().subtract(
                        1, 'week').endOf('week')],
                    '20 ke 19 bln ini': [moment().subtract(1, 'month').set("date", 20), moment()
                        .set("date", 19)
                    ],
                    '20 ke 19 bln lalu': [moment().subtract(2, 'month').set("date", 20), moment()
                        .subtract(1, 'month').set("date", 19)
                    ],
                }
            }, cb);
            cb();
            document.getElementById("select_range").value = null;
        });
    })(jQuery);

</script>
@endsection
