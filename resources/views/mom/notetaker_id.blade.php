@extends('layouts.master')
@section('title', $activity->title)

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
        vertical-align: top;
    }

    table.dataTable td:nth-child(2) {
        max-width: 250px;
        min-width: 200px;
    }

    table.dataTable td:nth-child(3) {
        max-width: 90px;
        width: 90px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    table.dataTable td {
        word-wrap: break-word;
        word-break: break-word;
    }

</style>
@endsection

@section('breadcrumb-items')
<span class="text-muted fw-light">Notulensi / Notulen / </span>
@endsection

@section('content')
<!-- Header -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                <div class="flex-grow-1 mt-4">
                    <div
                        class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                        <div class="user-profile-info">
                            <h4><b>RISALAH</b> {{ strtoupper($activity->title) }}</h4>
                            <ul
                                class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                                <li class="list-inline-item fw-semibold">
                                    <i class='bx bx-calendar'></i>
                                    {{ \Carbon\Carbon::parse($activity->date)->translatedFormat("l, d F Y") }}
                                </li>
                                <li class="list-inline-item fw-semibold">
                                    <i class='bx bx-map-pin'></i>
                                    {{ $activity->location }}
                                </li>
                                <li class="list-inline-item fw-semibold">
                                    <i class='bx bx-user-voice'></i>
                                    {{ $activity->host }}
                                </li>
                                <li class="list-inline-item fw-semibold">
                                    <span class="me-1"><i class='bx bx-group'></i>
                                        {{ $activity->participant }}
                                </li>
                            </ul>
                        </div>
                        <a onclick="window.print()" class="btn btn-light m-0 mt-2 p-0 text-info d-none d-md-block"
                            title="Cetak halaman ini">
                            <i class="bx bx-printer"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">

    </div>
</div>
<!--/ Header -->
<div class="row">
    <!-- Details Data -->
    <div class="col-12 mb-md-0 mb-4">
        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="table table-hover table-sm" id="datatable" width="100%">
                    <thead>
                        <tr>
                            <th width="20px" data-priority="1">No</th>
                            <th data-priority="2">Uraian Rapat</th>
                            <th>PIC</th>
                            <th width="80px">Target</th>
                            <th data-priority="3" width="40px">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="card-footer d-none d-md-block">
                <!-- <div class="mt-3"> -->
                <a class="btn btn-outline-secondary" href="{{ url()->previous() }}"><i
                        class="bx bx-chevron-left me-sm-2"></i> Kembali</a>
                <!-- </div> -->
            </div>
        </div>
    </div>
    <!-- /Details Data -->
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
            lengthMenu: [
                [-1, 3, 10, 50],
                ['Semua', 3, 10, 50],
            ],
            language: {
                searchPlaceholder: 'Cari uraian rapat..',
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            ajax: {
                url: "{{ route('mom.notetaker_id_data', ['id' => $activity->id]) }}",
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
                        return $("<textarea/>").html(row.detail).text();
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        var x = '';
                        if (row.pics != null) {
                            row.pics.forEach((e) => {
                                x += '<i class="badge rounded-pill bg-label-secondary" title="' +
                                    e.name + '">' + e.name + '</i><br>';
                            });
                        }
                        return x;
                    },
                },
                {
                    data: 'target',
                    name: 'target'
                },
                {
                    render: function (data, type, row, meta) {
                        return `<a class="text-success" title="Edit" style="cursor:pointer" onclick="EditId(` + row.id +
                            `)"><i class="bx bxs-edit"></i></a>
                                <a class="text-danger" title="Hapus" style="cursor:pointer" onclick="DeleteId(` + row.id +
                            `)" ><i class="bx bx-trash"></i></a>`;
                    },
                    className: "text-center"
                }
            ]
        });
    });

</script>
@endsection
