@extends('layouts.master')
@section('title', $data->name )
@section('breadcrumb-items')
<span class="text-muted fw-light">Akun / Profil / </span>
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('style')
<style>
    table.dataTable tbody td {
        vertical-align: middle;
    }

    table.dataTable td:nth-child(2) {
        max-width: 250px;
    }

    table.dataTable td:nth-child(3) {
        max-width: 100px;
    }
    table.dataTable td:nth-child(4) {
        max-width: 50px;
    }

    table.dataTable td:nth-child(5) {
        max-width: 50px;
    }
 

    table.dataTable td {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }


</style>
@endsection

@section('content')
@include('user._header_by_id')
<!-- User Profile Content -->
<div class="row">
    <div class="col-12">
        <!-- Projects table -->
        <div class="card mb-4">
            <div class="card-datatable table-responsive">
                <div class="card-header flex-column pb-0">
                    <div class="row">
                        <div class="col-md-7">
                            <h5>Riwayat Absensi</h5>
                        </div>
                    </div>
                </div>
                <table class="table table-hover table-sm" id="datatable" width="100%">
                    <thead>
                        <tr>
                            <th width="20px" data-priority="1">No</th>
                            <th data-priority="2">Aktivitas</th>
                            <th>Lokasi</th>
                            <th>Pimpinan Rapat/Acara</th>
                            <th data-priority="3" width="80px">Tanggal</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <!--/ Projects table -->
    </div>
</div>
<!--/ User Profile Content -->
@endsection
@section('script')
<script src="{{asset('assets/vendor/libs/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables.responsive.js')}}"></script>
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
            // bFilter: false,
            language: {
                searchPlaceholder: 'Cari..',
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            ajax: {
                url: "{{ route('user.data_by_id', ['id' => $data->username]) }}",
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
                        if (row.activity != null) {
                            return "<span title='" + row.activity.title + " " + row.activity.sub_title + "'>" + row.activity.title + " " + row.activity.sub_title + "</span>";
                        }
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        if (row.activity != null) {
                            var html = "<span title='" + row.activity.location + "'>" + row.activity.location + "</span>";
                            return html;
                        }
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        if (row.activity != null) {
                            var html = "<span title='" + row.activity.host + "'>" + row.activity.host + "</span>";
                            return html;
                        }
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        if (row.activity != null) {
                            var html = "<span title='" + row.activity.date + "'>" + row.activity.date + "</span>";
                            return html;
                        }
                    },
                    className: "text-md-center"
                },
            ]
        });
    });

</script>
@endsection
