@extends('layouts.master')
@section('title', 'Repository Digital')

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
            <label class="form-label">Nama <i class="text-danger">*</i></label>
            <div class="input-group mb-3">
                <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama"
                    placeholder="Nama File" value="{{ old('nama') }}">
                @error('nama')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Status Publikasi <i class="text-danger">*</i></label>
            <div class="input-group mb-3">
                <select id="select_published" class="select2 form-select" name="status_publikasi" data-placeholder="Tidak dipublikasikan">
                    <option value=0>Tidak (File Pribadi)</option>
                    <option value=1>Ya (Dipublikasikan ke user lain)</option>
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <label class="form-label">Pilih File <i class="text-danger">*</i></label>
            <div class="input-group mb-3">
                <input class="form-control @error('file_repositori') is-invalid @enderror"
                                            name="file_repositori" type="file"
                                            accept=".jpg, .jpeg, .png, .pdf"
                                            title="JPG/PNG">
                <button class="btn btn-outline-primary" type="submit" id="button-addon2">Unggah Sekarang!</button>
                @error('file_repositori')
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
                    <th data-priority="2">Nama File</th>
                    <th>Path</th>
                    <th width="50px" >Tipe</th>
                    <th width="50px" >Dipublikasikan</th>
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
                searchPlaceholder: 'Cari ..',
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            ajax: {
                url: "{{ route('REPOSITORY.data') }}",
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
                        return `<a target="_blank" href="{{ asset('` +
                            row.file_path + `') }}" title="`+ row.name + `">`+row.name+`</a>`;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return '<button class="btn m-0 p-0" title="Salin" onclick=navigator.clipboard.writeText("{{ url('') }}/repo/' + row.uid + '")><i class="bx bx-copy"></i></button> ' + 
                        `<span class="text-muted">{{ url('') }}/repo/</span>` +  row.uid ;
                    },
                },
                {
                    
                    render: function (data, type, row, meta) {
                        return row.type;
                    },
                },
                {
                    
                    render: function (data, type, row, meta) {
                        if(row.published == true){
                            return '<a class="text-success"><i class="bx bx-check-circle"></i> Ya</a>';
                        } else {
                            return '<a class="text-danger"><i class="bx bx-x-circle"></i> Tidak</a>';
                        }
                    },
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
                        var x = row.uid;
                        return `<a class="text-danger" title="Hapus" style="cursor:pointer" onclick="DeleteId('` + x +
                                `')" ><i class="bx bx-trash"></i></a>`;
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
                        url: "{{ route('REPOSITORY.delete') }}",
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
