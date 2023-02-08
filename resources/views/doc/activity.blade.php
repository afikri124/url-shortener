@extends('layouts.master')
@section('title', 'Aktivitas')

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

    table.dataTable td:nth-child(1) {
        max-width: 100px;
    }

    table.dataTable td:nth-child(2) {
        max-width: 200px;
    }

    table.dataTable td:nth-child(3) {
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
<div class="modal fade" id="modalEdit" style="display:none">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCenterTitle">Ubah</h5>
            </div>
            <form id="form-edit">
                @csrf
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-12 mb-0">
                            <label class="form-label">Penanggung Jawab</label>
                            <div class="select2-primary">
                                <select class="form-select select2-modalEdit" name="edit_user" id="edit_user">
                                    @foreach($user as $u)
                                    <option value="{{$u->user_id}}">{{ ucwords(strtolower($u->name)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mb-0">
                            <label class="form-label">Nama Aktivitas</label>
                            <input type="text" class="form-control" id="edit_aktivitas" name="edit_aktivitas">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="edit_id" id="edit_id" value="">
                    <button type="button" class="btn btn-label-secondary" onclick="CancelEdit()">Batal</button>
                    <button type="button" class="btn btn-success" onclick="SubmitEdit()">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="card mb-4">
    <form class="row p-3" method="POST" action="">
        @csrf
        <div class="col-md-4">
            <label class="form-label">Penanggung Jawab</label>
            <div class="input-group mb-3">
                <select class="form-select select2" name="penanggung_jawab" data-placeholder="-- Pilih PJ --">
                    <option value="">-- Pilih PJ --</option>
                    @foreach($user as $d)
                    <option value="{{ $d->user_id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
                @error('penanggung_jawab')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-md-8">
            <label class="form-label">Nama Aktivitas <i class="text-danger">*</i></label>
            <div class="input-group mb-3">
                <input type="text" name="aktivitas" class="form-control @error('aktivitas') is-invalid @enderror"
                    value="{{ old('aktivitas') }}">
                <button class="btn btn-outline-primary" type="submit" id="button-addon2">Tambah!</button>
                @error('aktivitas')
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
                    <th>Penanggung Jawab</th>
                    <th data-priority="2">Aktivitas</th>
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
                url: "{{ route('DOC.activity_data') }}",
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
                        if (row.user != null) {
                            return row.user.name;
                        }
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return row.name;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return `<a class="text-success" title="Ubah" style="cursor:pointer" onclick="EditId(` +
                            row.id +
                            `)"><i class="bx bxs-edit"></i></a>
                                <a class="text-danger" title="Hapus" style="cursor:pointer" onclick="DeleteId(` + row
                            .id +
                            `)" ><i class="bx bx-trash"></i></a>`;
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
                        url: "{{ route('DOC.activity_delete') }}",
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


    function CancelEdit() {
        $('#modalEdit').modal('hide');
        document.getElementById("form-edit").reset();
        $("#edit_user").trigger('change');
    }

    function EditId(id) {
        $.ajax({
            url: "{{ route('DOC.activity_id') }}",
            type: "POST",
            data: {
                "id": id,
                "_token": $("meta[name='csrf-token']").attr("content"),
            },
            success: function (data) {
                if (data['success']) {
                    $('#edit_id').val(id);
                    $('#edit_aktivitas').val(data['data'].name);
                    $('#edit_user').val(data['data'].user_id).trigger("change");
                    $('#modalEdit').modal('show');
                    // console.log(data);
                } else {
                    swal(data['message'], {
                        icon: "error",
                    });
                }
            }
        })
    }

    function SubmitEdit() {
        $.ajax({
            url: "{{ route('DOC.activity_edit') }}",
            type: "POST",
            data: {
                "_token": $("meta[name='csrf-token']").attr("content"),
                "id": $('#edit_id').val(),
                "name": $("#edit_aktivitas").val(),
                "user_id": $("#edit_user").val(),
            },
            success: function (data) {
                if (data['success']) {
                    swal(data['message'], {
                        icon: "success",
                    });
                    $('#datatable').DataTable().ajax.reload();
                    CancelEdit();
                } else {
                    swal(data['message'], {
                        icon: "error",
                    });
                }
            }
        })
    }

</script>
@endsection
