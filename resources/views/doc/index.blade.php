@extends('layouts.master')
@section('title', 'Unggah Bukti')

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
    table.dataTable tbody td {
        vertical-align: middle;
    }
    table.dataTable td:nth-child(2) {
        max-width: 200px;
    }
    
    table.dataTable td:nth-child(4) {
        max-width: 150px;
    }

    table.dataTable td:nth-child(5) {
        max-width: 50px;
    }

    table.dataTable td:nth-child(6) {
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
                        <form method="POST" class="row" target="_blank" action="">
                            @csrf
                            <div class=" col-md-3">
                                <select id="select_activitas" class="select2 form-select" name="aktivitas" data-placeholder="Aktivitas">
                                    <option value="">Aktivitas</option>
                                    @foreach($activity as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-3">
                                <select id="select_kategori" class="select2 form-select" name="kategori" data-placeholder="Kategori">
                                    <option value="">Kategori</option>
                                    @foreach($category as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-3">
                                <select id="select_status" class="select2 form-select" name="status" data-placeholder="Status">
                                    <option value="">Status</option>
                                    @foreach($status as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 text-md-end text-center pt-3 pt-md-0">
                                @if(Auth::user()->hasRole('DS'))
                                <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas"
                                    data-bs-target="#newrecord" aria-controls="offcanvasEnd" tabindex="0"
                                    aria-controls="DataTables_Table_0" type="button"><span><i
                                            class="bx bx-plus me-sm-2"></i>
                                        <span>Tambah</span></span>
                                </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-hover table-sm" id="datatable" width="100%">
            <thead>
                <tr>
                    <th width="20px" data-priority="1">No</th>
                    <th data-priority="2">Dokumen yg diperlukan</th>
                    <th width="100px">Batas Waktu</th>
                    <th>Penanggung Jawab</th>
                    <th data-priority="4">Status</th>
                    <th width="50px" data-priority="3">Aksi</th>
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
                url: "{{ route('DOC.index_data') }}",
                data: function (d) {
                    // d.select_pembuat = $('#select_pembuat').val(),
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
                        return `<span title='` + row.name + `'>` + row.name + `</span>`;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return row.deadline;
                    },
                    className: "text-md-center"
                },  
                {
                    render: function (data, type, row, meta) {
                        // if (row.user != null) {
                        //     return "<span title='" + row.user.name + "'>" + row.user.name +
                        //         "</span>";
                        // }
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return "<span title='" + row.status_id + "'>" + row.status_id + "</span>";
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return `<a class="text-success btn btn-light btn-sm" title="Tulis Notulensi" href="{{ url('MoM/note-taker/` + row.idd +  `') }}"><i class="bx bxs-edit"></i></a>`;
                    },
                    className: "text-center"
                }
            ]
        });
        
        $('#select_pembuat').change(function () {
            table.draw();
        });
    });

</script>
@endsection
