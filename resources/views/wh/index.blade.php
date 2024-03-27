@extends('layouts.master')
@section('title', 'Jam Kerja')

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
<div class="alert alert-secondary alert-dismissible" role="alert" id="lastupdate">
    Data terakhir disinkronkan {{ ($lastData != null ? \Carbon\Carbon::parse($lastData->timestamp)->translatedFormat("l, d F Y H:i") : "");}}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<div class="card" id="section-block">
    <div class="card-datatable table-responsive">
        <div class="card-header flex-column flex-md-row pb-0">
            <div class="row">
                <div class="col-12 pt-3 pt-md-0">
                    <div class="col-12">
                        <form method="POST" action="" target="_blank">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" id="select_range" name="range" class="form-control @error('range') is-invalid @enderror"
                                        placeholder="Pilih Tanggal" autocomplete="off"/>
                                        @error('range')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                </div>
                                @if(Auth::user()->hasRole('HR'))
                                <div class=" col-md-3">
                                    <select id="select_user" name="select_user" class="select2 form-select @error('select_user') is-invalid @enderror" data-placeholder="Pilih Akun">
                                        <option value="">Pilih Akun</option>
                                        @foreach($user as $d)
                                        <option value="{{ ($d->username == null ? $d->username_old:$d->username) }}" @if($d->username == Auth::user()->username) selected @endif>
                                            {{ ($d->user==null ? "[".$d->name."]" : $d->user->name )}}</option>
                                        @endforeach
                                    </select>
                                    @error('select_user')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class=" col-md-2">
                                </div>
                                @else
                                <div class=" col-md-5">
                                </div>
                                @endif
                                <div class="col-md-4 text-md-end text-center pt-3 pt-md-0">
                                    @if(Auth::user()->hasRole('HR'))
                                    <button class="btn btn-outline-secondary" type="button" onclick="SyncAtt()">
                                        <span><i class="bx bx-sync me-sm-2"></i>
                                            Sinkron</span>
                                    </button>
                                    @endif
                                    <button class="btn btn-primary" type="submit" title="Cetak Laporan">
                                        <span><i class="bx bx-printer me-sm-2"></i> Cetak</span>
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-hover table-sm text-md-center stripe" id="datatable" width="100%">
            <thead>
                <tr>
                    <th width="30px" data-priority="1">No</th>
                    <th data-priority="2">Nama<br><code>Userid @ Mesin</code></th>
                    <th width="60px">Hari/Tgl</th>
                    <th width="60px">Masuk</th>
                    <th width="60px">Keluar</th>
                    <th width="60px">Telat</th>
                    <th width="80px">Plg Cepat</th>
                    <th width="60px">Jam Lembur</th>
                    <th width="60px">Jam Kurang</th>
                    <th width="80px" data-priority="3">Total Jam/Hari</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th width="30px" colspan="2">Total</th>
                    <th width="60px"></th>
                    <th width="60px"></th>
                    <th width="60px"></th>
                    <th width="60px"></th>
                    <th width="80px"></th>
                    <th width="60px"></th>
                    <th width="60px"></th>
                    <th width="80px" id="Total_All" class="text-center"></th>
                </tr>
            </tfoot>
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

    function total_all() {
        // alert($('#select_user').val()+$('#select_range').val());
        $.ajax({
            url: "{{ route('WH.total_h') }}",
            type: "GET",
            data: {
                "_token": $("meta[name='csrf-token']").attr("content"),
                "select_user": $('#select_user').val(),
                "select_range": $('#select_range').val()
            },
            success: function (data) {
                if (data['success']) {
                    var jam = "0:0";
                    if (data['total'] != null) {
                        jam = data['total'].split(":");
                    }
                    if (jam[0] < 160) {
                        document.getElementById("Total_All").innerHTML = "<b class='text-danger'>" + jam[
                            0] + " Jam</b>";
                    } else {
                        document.getElementById("Total_All").innerHTML = "<b class='text-success'>" + jam[
                            0] + " Jam</b>";
                    }
                } else {
                    document.getElementById("Total_All").innerHTML = "Error!";
                }
            }
        })
    }

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
                url: "{{ route('WH.data') }}",
                data: function (d) {
                    d.select_user = $('#select_user').val(),
                        d.select_range = $('#select_range').val(),
                        d.search = $('input[type="search"]').val()
                },
            },
            scroller: {
                loadingIndicator: true
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
                        var html = `<code>` + row.username + `</code>`;
                        if (row.user != null) {
                            html = `<b>` + row.user
                                .name + `</b><br>` + html;
                        } else {
                            html = `<small title='Nama di Mesin'>[` + row.name +
                                `]</small><br>` + html;
                        }
                        return html;
                    },
                    className: "text-start"
                },
                {
                    render: function (data, type, row, meta) {
                        return moment(row.tanggal).format('dddd<br>L');
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        if (row.masuk != row.keluar) {
                            return moment(row.masuk).format('H:mm');
                        } else if (moment(row.masuk) <= moment(row.tanggal + " 16:00")) {
                            return moment(row.masuk).format('H:mm');
                        }
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        if (row.masuk != row.keluar) {
                            return moment(row.keluar).format('H:mm');
                        } else if (moment(row.keluar) > moment(row.tanggal + " 16:00")) {
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
                },
                {
                    data: 'kurang',
                    name: 'kurang'
                }, 
                {
                    data: 'total_jam',
                    name: 'total_jam'
                }
            ]
        });
        $('#select_user').change(function () {
            table.draw();
            total_all();
        });
        $('#select_range').change(function () {
            table.draw();
            total_all();
        });
    });

</script>
@if(Auth::user()->hasRole('HR'))
<script type="text/javascript">
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
                            $.blockUI({
                                message: '<div class="spinner-border text-white" role="status">s.jgu</div><br>Tunggu Sebentar..<br>Menyinkronkan data dari mesin Absensi.',
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
                            document.getElementById('lastupdate').style.display = 'none';
                            $.unblockUI();
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
@endif

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
