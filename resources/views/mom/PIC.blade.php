@extends('layouts.master')
@section('title', 'PIC Uraian Rapat')

@section('breadcrumb-items')
<span class="text-muted fw-light">Notulensi /</span>
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

    p {
        margin-bottom: 0;
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
        <table class="table table-hover table-sm" id="datatable" width="100%">
            <thead>
                <tr>
                    <th width="20px" data-priority="1">No</th>
                    <th data-priority="2">Uraian Rapat</th>
                    <th >PIC</th>
                    <th width="80px">Target</th>
                    <th data-priority="3" width="50px">Aksi</th>
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
            lengthMenu: [
                [5, 10, 100],
                [5, 10, 100],
            ],
            language: {
                searchPlaceholder: 'Cari uraian rapat..',
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            ajax: {
                url: "{{ route('mom.PIC_data') }}",
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
                        activity = "";
                        if (row.activity != null) {
                            activity = "<strong title='" + row.activity.title + "'>" + row.activity.title +
                                "</strong> <span class='badge rounded-pill bg-label-secondary'>" + row.activity.date + "</span><br>";
                        }                       
                        return activity + $("<textarea/>").html(row.detail).text();
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        var x = '';
                        if (row.pics != null) {
                            row.pics.forEach((e) => {
                                x += '<i class="badge rounded-pill bg-label-secondary" title="' + e.name +'">' + e.name + '</i><br> ';
                            });
                        }
                        return x;
                    },
                },
                {data: 'target', name: 'target'},
                {
                    render: function (data, type, row, meta) {
                        return `<a class="text-primary btn btn-light btn-sm" title="Lihat" href="{{ url('MoM/PIC/` + row.idd +  `') }}"><i class="bx bx-show"></i></a>`;
                    },
                    className: "text-center"
                }
            ]
        });
    });

</script>
@endsection
