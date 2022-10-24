@extends('layouts.master')
@section('title', 'Profile')
@section('breadcrumb-items')
<span class="text-muted fw-light">User /</span>
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
        max-width: 200px;
    }

    table.dataTable td {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

</style>
@endsection

@section('content')
@include('user._header')
<!-- User Profile Content -->
<div class="row">
    <div class="col-xl-5 col-lg-5 col-md-5">
        <!-- About User -->
        <div class="card mb-4">
            <div class="card-body">
                <small class="text-muted text-uppercase">About</small>
                <ul class="list-unstyled mb-4 mt-3">
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-user"></i><span
                            class="fw-semibold mx-2">Name:</span>
                        <span>{{ Auth::user()->name }}</span></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-user-check"></i><span
                            class="fw-semibold mx-2">Username:</span>
                        <span>{{ Auth::user()->username }}</span></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-mail-send"></i><span
                            class="fw-semibold mx-2">Email:</span>
                        <span>{{ Auth::user()->email }}</span></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-briefcase"></i><span
                            class="fw-semibold mx-2">Job:</span>
                        <span>{{ Auth::user()->job }}</span></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-face"></i><span
                            class="fw-semibold mx-2">Gender:</span>
                        <span>{{ Auth::user()->gender }}</span></li>
                </ul>
            </div>
        </div>
        <!--/ About User -->
        <!-- Profile Overview -->
        <div class="card mb-4">
            <div class="card-body">
                <small class="text-muted text-uppercase">Role Access</small><br>
                <ul class="list-unstyled mb-4 mt-3">
                    <span>
                        @if(Auth::user()->roles->count() == 0)
                        <p class="p-0 mb-0 text-danger">You don't have access rights, please contact the administrator!</p>
                        @else
                        @foreach(Auth::user()->roles as $x)
                        <i class="badge bg-{{ $x->color }} m-0">{{ $x->title }}</i>
                        @endforeach
                        @endif
                    </span>
                </ul>
            </div>
        </div>
        <!--/ Profile Overview -->
    </div>
    <div class="col-xl-7 col-lg-7 col-md-7">
        <!-- Projects table -->
        <div class="card mb-4">
            <div class="card-datatable table-responsive">
                <div class="card-header flex-column pb-0">
                    <div class="row">
                        <div class="col-md-5">
                            <h5>Attendances</h5>
                        </div>
                    </div>
                </div>
                <table class="table table-hover table-sm" id="datatable" width="100%">
                    <thead>
                        <tr>
                            <th width="20px" data-priority="1">No</th>
                            <th data-priority="2">Activity</th>
                            <th width="40px" data-priority="3">Date</th>
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
                searchPlaceholder: 'Search..',
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            ajax: {
                url: "{{ route('user.data') }}",
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
                            return row.activity.date;
                        }
                    },
                    className: "text-md-center"
                }
            ]
        });
    });

</script>
@endsection
