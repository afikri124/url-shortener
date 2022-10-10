@extends('layouts.master')
@section('title', 'URL Shortener')

@section('breadcrumb-items')
<!-- <span class="text-muted fw-light">Pusat Data /</span> -->
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

    table.dataTable td:nth-child(3) {
        max-width: 200px;
    }

    table.dataTable td:nth-child(4) {
        max-width: 20px;
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
<div class="card mb-4">
    <form class="row p-3" method="POST" action="">
        @csrf
        <div class="col-md-6">
            <label class="form-label">Shortlink</label>
            <div class="input-group mb-3">
                <span class="input-group-text">
                    s.jgu.ac.id/
                </span>
                <input type="text" name="shortlink" class="form-control @error('shortlink') is-invalid @enderror"
                    value="{{ old('shortlink') }}" placeholder="something">
                @error('shortlink')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Long URL</label>
            <div class="input-group mb-3">
                <input type="url" class="form-control @error('url') is-invalid @enderror" name="url"
                    placeholder="http://.." value="{{ old('url') }}">
                <button class="btn btn-outline-primary" type="submit" id="button-addon2">Make it Now!</button>
                @error('url')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </form>
</div>
<div class="card">
    <div class="card-datatable table-responsive">
        <table class="table table-hover table-sm" id="datatable" width="100%">
            <thead>
                <tr>
                    <th width="20px" data-priority="1">No</th>
                    <th data-priority="2">Shortlink</th>
                    <th>Long URL</th>
                    <th>QRCode</th>
                    <th>Maker</th>
                    <th width="85px">Action</th>
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
    setTimeout(function () {
        (function ($) {
            "use strict";
            $(".select2-modal").select2({
                dropdownParent: $('#newrecord'),
                allowClear: true,
                minimumResultsForSearch: 5
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
                searchPlaceholder: 'Search shortlink..',
            },
            ajax: {
                url: "{{ route('url.data') }}",
                data: function (d) {
                    d.select_dosen = $('#select_dosen').val(),
                        d.select_kategori = $('#select_kategori').val(),
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
                        return '<button class="btn m-0 p-0" title="Copy" onclick=navigator.clipboard.writeText("s.jgu.ac.id/' + row.shortlink + '")><i class="bx bx-copy"></i></button> ' + 
                        `<a class="text-primary" target="_blank" href="{{ url('` +
                            row.shortlink + `') }}">` +  row.shortlink +`</a>`;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return `<a class="text-primary" target="_blank" href="` + row.url + `">` + row.url + `</a>`;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        var x = `{{ url('` + row.shortlink + `') }}`;
                        var l = 's.jgu.ac.id/' + row.shortlink;
                        return `<a class="text-dark" target="_blank" href="{{ url('qrcode?data=` +
                            x + `&label=` + l + `') }}" title="View QRCode"><i class="bx bx-qr-scan"></i></a>`;
                    },
                    className: "text-md-center"
                },
                
                {
                    render: function (data, type, row, meta) {
                        if(row.user != null){
                            return html = row.user.name;
                        }
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        if(row.user_id == "{{Auth::user()->id}}"){
                            return `<a class="text-success" title="Edit" href="{{ url('URL/edit/` +
                                row.idd + `') }}"><i class="bx bxs-edit"></i></a>
                                <a class="text-danger" title="Delete" onclick="DeleteId(` + row.id +
                                `)" ><i class="bx bx-trash"></i></a>`;
                        } else {
                            return `<a class="text-muted"><i class="bx bxs-edit"></i></a>
                                <a class="text-muted"><i class="bx bx-trash"></i></a>`;
                        }
                    },
                    className: "text-md-center"
                }

            ]
        });
    });

    function DeleteId(id) {
        swal({
                title: "Are you sure?",
                text: "Once deleted, the data cannot be recovered!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('url.delete') }}",
                        type: "DELETE",
                        data: {
                            "id": id,
                            "_token": $("meta[name='csrf-token']").attr("content"),
                        },
                        success: function (data) {
                            if (data['success']) {
                                swal(data['message'], {
                                    icon: "success",
                                });
                                $('#datatable').DataTable().ajax.reload();
                            } else {
                                swal(data['message'], {
                                    icon: "error",
                                });
                            }
                        }
                    })
                }
            })
    }

</script>
@endsection
