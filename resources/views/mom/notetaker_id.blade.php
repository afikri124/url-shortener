@extends('layouts.master')
@section('title', $activity->title)

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/sweetalert2.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/typography.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/editor.css')}}" />
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

    table.dataTable td:nth-child(4) {
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

    div.dt-buttons {
        float: right;
        position: relative;
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
                        class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-3 mb-0 flex-md-row flex-column gap-4">
                        <div class="user-profile-info">
                            <h4><b>RISALAH RAPAT</b><br>{{ strtoupper($activity->title) }}</h4>
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
                        <div class="text-md-end text-center">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                <span><i class="bx bx-plus me-sm-2"></i> Tambah</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalTambah">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Tambah Uraian Rapat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-tambah">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-md-6 mb-0">
                                <label for="add_target" class="form-label">Target</label>
                                <input type="date" class="form-control" id="add_target" name="add_target" placeholder="yyyy-mm-dd">
                            </div>
                            <div class="col-md-6 mb-0">
                                <label for="add_users" class="form-label">Penanggung Jawab (PIC)</label>
                                <div class="select2-primary">
                                    <select class="form-select select2-modal" multiple name="add_users[]" id="add_users"
                                        data-placeholder="Pilih Penanggung Jawab (PIC)">
                                        @foreach($users as $u)
                                        <option value="{{$u->id}}">{{ ucwords(strtolower($u->name)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col mb-3">
                                <label for="myeditor" class="form-label">Uraian Rapat</label>
                                <div id="myeditor">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" onclick="CancelAdd()">Batal</button>
                        <button type="button" class="btn btn-primary" onclick="SubmitAdd()">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Ubah Uraian Rapat</h5>
                </div>
                <form id="form-edit">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-md-6 mb-0">
                                <label for="edit_target" class="form-label">Target</label>
                                <input type="date" class="form-control" id="edit_target" name="edit_target">
                            </div>
                            <div class="col-md-6 mb-0">
                                <label for="edit_users" class="form-label">PIC <span class="text-danger">(Kosongkan jika
                                        tidak ada
                                        perubahan)</span></label>
                                <div class="select2-primary">
                                    <select class="form-select select2-modalEdit" multiple name="edit_users[]"
                                        id="edit_users" data-placeholder="Kosongkan Jika Tidak Ada Perubahan">
                                        @foreach($users as $u)
                                        <option value="{{$u->id}}">{{ ucwords(strtolower($u->name)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col mb-3">
                                <label for="myeditor2" class="form-label">Uraian Rapat</label>
                                <div id="myeditor2">

                                </div>
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
</div>
<!--/ Header -->
@if(session('msg'))
<div class="alert alert-primary alert-dismissible" role="alert">
    {{session('msg')}}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <!-- Details Data -->
    <div class="col-12 mb-md-0 mb-4">
        <div class="card mb-4">
            <div class="card-datatable table-responsive">
                <table class="table table-hover table-sm" id="datatable" width="100%">
                    <thead>
                        <tr>
                            <th width="20px" data-priority="1">No</th>
                            <th data-priority="2">Uraian Rapat</th>
                            <th width="80px">Target</th>
                            <th>PIC</th>
                            <th data-priority="3" width="50px">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- /Details Data -->
</div>

<div class="row">
    <div class="col-12 mb-md-0 mb-4">
        <div class="card">
            <div class="card-datatable table-responsive">
                <div class="flex-column flex-md-row pb-0">
                    <div class="offcanvas offcanvas-bottom @if($errors->all()) show @endif" tabindex="-1" id="newrecord"
                        aria-labelledby="offcanvasEndLabel">
                        <div class="offcanvas-header">
                            <h5 id="offcanvasEndLabel" class="offcanvas-title">Tambah Dokumen</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body my-auto mx-0 flex-grow-0">
                            <form class="add-new-record pt-0 row g-2 fv-plugins-bootstrap5 fv-plugins-framework"
                                enctype="multipart/form-data" id="form-add-new-record" method="POST" action="">
                                @csrf
                                <div class="col-12 fv-plugins-icon-container">
                                    <label class="form-label" for="dokumen">Dokumen/Gambar</label>
                                    <div class="input-group input-group-merge has-validation">
                                        <input class="form-control @error('dokumen') is-invalid @enderror"
                                            name="dokumen" type="file"
                                            accept=".xlsx,.xls, .jpg, .jpeg, .png, .doc, .docx,.ppt, .pptx, .pdf"
                                            title="Image/pdf/doc/xls">

                                        <button type="submit" class="btn btn-primary" type="submit"
                                            id="button-addon2">Unggah !</button>
                                        @error('dokumen')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0 px-3">
                    <table class="table table-hover table-sm" id="mom_docs" width="100%">
                        <thead>
                            <tr>
                                <th width="20px" data-priority="1">No</th>
                                <th data-priority="2">Nama File</th>
                                <th>Tipe</th>
                                <th width="85px" data-priority="3">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="card-footer d-none d-md-block">
                    <a class="btn btn-outline-secondary" href="{{ url()->previous() }}"><i
                            class="bx bx-chevron-left me-sm-2"></i> Kembali</a>
                </div>
            </div>
        </div>
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
<script src="{{asset('assets/js/ui-modals.js')}}"></script>
<script src="{{asset('assets/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('assets/vendor/libs/quill/quill.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script>
    "use strict";
    var quill = new Quill("#myeditor", {
        bounds: "#myeditor",
        placeholder: "Ketik Sesuatu...",
        modules: {
            formula: !0,
            toolbar: [
                [{
                    font: []
                }, {
                    size: []
                }],
                ["bold", "italic", "underline", "strike"],
                [{
                    color: []
                }, {
                    background: []
                }],
                [{
                    script: "super"
                }, {
                    script: "sub"
                }],
                [{
                    header: "1"
                }, {
                    header: "2"
                }, "blockquote", "code-block"],
                [{
                    list: "ordered"
                }, {
                    list: "bullet"
                }, {
                    indent: "-1"
                }, {
                    indent: "+1"
                }],
                ["direction", {
                    align: []
                }],
                ["link", "image", "video", "formula"],
                ["clean"]
            ]
        },
        theme: "snow"
    });

    var quill2 = new Quill("#myeditor2", {
        bounds: "#myeditor2",
        placeholder: "Ketik Sesuatu...",
        modules: {
            formula: !0,
            toolbar: [
                [{
                    font: []
                }, {
                    size: []
                }],
                ["bold", "italic", "underline", "strike"],
                [{
                    color: []
                }, {
                    background: []
                }],
                [{
                    script: "super"
                }, {
                    script: "sub"
                }],
                [{
                    header: "1"
                }, {
                    header: "2"
                }, "blockquote", "code-block"],
                [{
                    list: "ordered"
                }, {
                    list: "bullet"
                }, {
                    indent: "-1"
                }, {
                    indent: "+1"
                }],
                ["direction", {
                    align: []
                }],
                ["link", "image", "video", "formula"],
                ["clean"]
            ]
        },
        theme: "snow"
    });

    "use strict";
    setTimeout(function () {
        (function ($) {
            $(".select2-modal").select2({
                dropdownParent: $('#modalTambah'),
                allowClear: true
            });
        })(jQuery);
    }, 350);

    "use strict";
    setTimeout(function () {
        (function ($) {
            $(".select2-modalEdit").select2({
                dropdownParent: $('#modalEdit'),
                allowClear: true
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
                    d.search = $('.dataTables_filter input').val();
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
                    data: 'target',
                    name: 'target'
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
                    render: function (data, type, row, meta) {
                        return `<a class="text-success" title="Ubah" style="cursor:pointer" onclick="EditId(` +
                            row.id +
                            `)"><i class="bx bxs-edit"></i></a>
                                <a class="text-danger" title="Hapus" style="cursor:pointer" onclick="DeleteId(` + row
                            .id +
                            `)" ><i class="bx bx-trash"></i></a>`;
                    },
                    className: "text-center"
                }
            ]
        });
    });

    function DeleteId(id) {
        swal({
                title: "Apakah Anda yakin?",
                text: "Ketika dihapus, data tidak dapat dikembalikan!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('mom.notetaker_delete') }}",
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

    function SubmitAdd() {
        var detailX = $('#myeditor .ql-editor').html();
        if (detailX == '<p><br></p>') {
            swal("Uraian Rapat tidak boleh kosong..", {
                icon: "error",
            });
        } else {
            $.ajax({
                url: "{{ route('mom.notetaker_add') }}",
                type: "POST",
                data: {
                    "_token": $("meta[name='csrf-token']").attr("content"),
                    "activity_id": "{{ $activity->id }}",
                    "target": $("#add_target").val(),
                    "detail": detailX,
                    "users": $("#add_users").val(),
                },
                success: function (data) {
                    if (data['success']) {
                        swal(data['message'], {
                            icon: "success",
                        });
                        $('#datatable').DataTable().ajax.reload();
                        CancelAdd();
                    } else {
                        swal(data['message'], {
                            icon: "error",
                        });
                    }
                }
            })
        }
    }

    function CancelAdd() {
        $('#modalTambah').modal('hide');
        document.getElementById("form-tambah").reset();
        quill.setContents([{
            insert: '\n'
        }]);
        $("#add_users").trigger('change');
    }

    function CancelEdit() {
        $('#modalEdit').modal('hide');
        document.getElementById("form-edit").reset();
        quill2.setContents([{
            insert: '\n'
        }]);
        $("#edit_users").trigger('change');
    }

    function EditId(id) {
        $.ajax({
            url: "{{ route('mom.list_id') }}",
            type: "POST",
            data: {
                "id": id,
                "_token": $("meta[name='csrf-token']").attr("content"),
            },
            success: function (data) {
                if (data['success']) {
                    document.getElementById('edit_target').value = data['data'].target;
                    document.getElementById('edit_id').value = id;
                    $("#edit_users").trigger('change');
                    var myEditor2 = document.querySelector("#myeditor2");
                    myEditor2.children[0].innerHTML = data['data'].detail;
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
        var detailX = $('#myeditor2 .ql-editor').html();
        if (detailX == '<p><br></p>') {
            swal("Uraian Rapat tidak boleh kosong..", {
                icon: "error",
            });
        } else {
            // alert(detailX);
            $.ajax({
                url: "{{ route('mom.notetaker_edit') }}",
                type: "POST",
                data: {
                    "_token": $("meta[name='csrf-token']").attr("content"),
                    "id": $('#edit_id').val(),
                    "target": $("#edit_target").val(),
                    "detail": detailX,
                    "users": $("#edit_users").val(),
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
    }

</script>

<script type="text/javascript">
    $(document).ready(function () {
        var table = $('#mom_docs').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ordering: false,
            bFilter: false,
            language: {
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            dom: '<"datatable-header"fBl<"toolbar mb-3">><t><"datatable-footer"ip>',
            buttons: [{
                text: '<span data-bs-toggle="offcanvas" data-bs-target="#newrecord" aria-controls="offcanvasEnd"><i class="bx bx-plus me-sm-2"></i> Dokumen</span>',
                className: 'btn btn-primary mb-2',
                // action: function (e, dt, node, config) {
                //     alert('Button activated');
                // }
            }],
            ajax: {
                url: "{{ route('mom.mom_docs') }}",
                data: function (d) {
                    d.activity_id = "{{ $activity->id }}"
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
                        return `<a href="{{ asset('') }}` + row.doc_path + `" target="_blank" ><span title='` + row.doc_path + `'>` + row.doc_path +
                            `</span></a>`;
                    },
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    render: function (data, type, row, meta) {
                        return `<a class="text-danger" title="Hapus" style="cursor:pointer" onclick="DeleteDocId(` +
                            row.id +
                            `)" ><i class="bx bx-trash"></i></a> `;
                    },
                    className: "text-md-center"
                }

            ]
        });
    });

    function DeleteDocId(id) {
        swal({
                title: "Apakah Anda yakin?",
                text: "Ketika dihapus, data tidak dapat dikembalikan!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('mom.mom_docs_delete') }}",
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
                                $('#mom_docs').DataTable().ajax.reload();
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
