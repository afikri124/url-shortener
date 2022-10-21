@extends('layouts.master')
@section('title', 'Account')

@section('breadcrumb-items')
<span class="text-muted fw-light">Setting /</span>
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
                                <select id="select_role" class="select2 form-select" data-placeholder="Roles">
                                    <option value="">Roles</option>
                                    @foreach($roles as $d)
                                    <option value="{{ $d->id }}">{{ $d->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-3">
                                <select id="select_job" class="select2 form-select" data-placeholder="Job">
                                    <option value="">Job</option>
                                    @foreach($job as $d)
                                    <option value="{{ $d->job }}">{{ $d->job }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-hover table-sm" id="datatable" width="100%">
            <thead>
                <tr>
                    <th width="20px" data-priority="1">No</th>
                    <th data-priority="2">Name</th>
                    <th>Email</th>
                    <th>Job</th>
                    <th>Access</th>
                    <th width="20px" data-priority="3">Action</th>
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
                searchPlaceholder: 'Search..',
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            ajax: {
                url: "{{ route('setting_account_data') }}",
                data: function (d) {
                        d.select_role = $('#select_role').val(),
                        d.select_job = $('#select_job').val(),
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
                        var html = `<a class="text-primary" title="` + row.name + `" href="{{ url('profile/` +
                            row.idd + `') }}">` + row.name + `</a>`;
                        return html;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        var html = "<span title='" + row.email + "'>" + row.email +
                            "</span>";
                        return html;
                    },
                },

                {
                    render: function (data, type, row, meta) {
                        if(row.job != null){
                            return "<span title='" + row.job + "'>" + row.job + "</span>";
                        }
                        
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        var x = '<ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">';
                        if(row.roles != null){
                            row.roles.forEach((e) => {
                                x += '<li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" class="avatar avatar-xs pull-up" title="' 
                                + e.title + '"><i class="badge rounded-pill bg-'+e.color+'"  style="font-size:8pt;">' + e.title + '</i></li>';
                            });
                        }
                        var y = "</ul>";
                        return x+y;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        var html = `<a class=" text-success" title="Ubah" href="{{ url('setting/account/edit/` +
                            row.idd + `') }}"><i class="bx bxs-edit"></i></a>`;
                        if (row.id != 1) {
                            return html;
                        } else {
                            return "";
                        }
                    },
                    className: "text-center"
                }
            ]
        });
        $('#select_role').change(function () {
            table.draw();
        });
        $('#select_job').change(function () {
            table.draw();
        });
    });


</script>
@endsection
