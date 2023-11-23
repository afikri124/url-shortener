@extends('layouts.master')
@section('title', 'Situs Mikro')

@section('breadcrumb-items')
<!-- <span class="text-muted fw-light">Data /</span> -->
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
<div class="card mb-4">
    <form class="row p-3" method="POST" action="" enctype="multipart/form-data">
        @csrf
        <div class="col-md-4">
            <label class="form-label">Judul <i class="text-danger">*</i></label>
            <div class="input-group mb-3">
                <input type="text" class="form-control @error('judul') is-invalid @enderror" name="judul"
                    placeholder="Judul Situs" value="{{ old('judul') }}">
                @error('judul')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Shortlink <i class="text-danger">*</i></label>
            <div class="input-group mb-3">
                <span class="input-group-text @error('shortlink') btn-danger @enderror">
                    s.jgu.ac.id/m/
                </span>
                <input type="text" name="shortlink" class="form-control @error('shortlink') is-invalid @enderror"
                    value="{{ old('shortlink') }}" placeholder="link pendek">
                @error('shortlink')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Avatar/Logo <i class="text-danger">*</i></label>
            <div class="input-group mb-3">
                <input class="form-control @error('avatar') is-invalid @enderror"
                                            name="avatar" type="file"
                                            accept=".jpg, .jpeg, .png"
                                            title="JPG/PNG">
                @error('avatar')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        
        <div class="col-md-12">
            <label class="form-label">Bio <i class="text-danger">*</i></label>
            <div class="input-group mb-3">
                <input type="text" class="form-control @error('bio') is-invalid @enderror" name="bio"
                    placeholder="Deskripsi Halaman (support tag html, ex: </br>)" value="{{ old('bio') }}">
                <button class="btn btn-outline-primary" type="submit" id="button-addon2">Buat Sekarang!</button>
                @error('bio')
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
                    <th data-priority="2">Judul</th>
                    <th>Shortlink</th>
                    <th>QRCode</th>
                    <th>Pembuat</th>
                    <th width="85px" data-priority="3">Aksi</th>
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
                searchPlaceholder: 'Cari Judul..',
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            ajax: {
                url: "{{ route('MICROSITE.data') }}",
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
                        return `<a target="_blank" href="{{ url('m/` +
                            row.shortlink + `') }}" title="`+ row.title + `">`+row.title+`</a>`;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return '<button class="btn m-0 p-0" title="Salin" onclick=navigator.clipboard.writeText("s.jgu.ac.id/m/' + row.shortlink + '")><i class="bx bx-copy"></i></button> ' + 
                        `<span class="text-muted">s.jgu.ac.id/</span><b>m/` +  row.shortlink +`</b>`;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        var x = `{{ url('`+ `m/` + row.shortlink + `') }}`;
                        var l = 's.jgu.ac.id/m/' + row.shortlink;
                        if(l.length > 30) {
                            l = row.shortlink;
                            if(l.length > 30) {
                                l = "";
                            }
                        }
                        return `<a class="text-dark" target="_blank" href="{{ url('qrcode?data=` +
                            x + `&label=` + l + `') }}" title="Lihat QR-Code"><i class="bx bx-qr-scan"></i></a>`;
                    },
                    className: "text-md-center"
                },
                
                {
                    render: function (data, type, row, meta) {
                        if(row.user != null){
                            return "<span title='" + row.user.name + "'>" + row.user.name + "</span>";
                        }
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        if(row.user_id == "{{Auth::user()->id}}"){
                            return `<a class="text-success" title="Edit" href="{{ url('MICROSITE/edit/` +
                                row.idd + `') }}"><i class="bx bxs-edit"></i></a> <a class="text-info" target="_blank" title="Print" style="cursor:pointer" href="{{ url('MICROSITE/print/` +
                                row.idd + `') }}"><i class="bx bxs-printer"></i></a> 
                                <a class="text-danger" title="Hapus" style="cursor:pointer" onclick="DeleteId(` + row.id +
                                `)" ><i class="bx bx-trash"></i></a>`;
                        } else {
                            return `<a class="text-muted"><i class="bx bxs-edit"></i></a> <a class="text-info" target="_blank" title="Print" style="cursor:pointer" href="{{ url('MICROSITE/print/` +
                                row.idd + `') }}"><i class="bx bxs-printer"></i></a> 
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
                title: "Apa kamu yakin?",
                text: "Setelah dihapus, data tidak dapat dipulihkan!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('MICROSITE.delete') }}",
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
