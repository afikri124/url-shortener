@extends('layouts.master')
@section('title', $data->title)

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

@section('breadcrumb-items')
<span class="text-muted fw-light">Situs Mikro / Edit /</span>
@endsection


@section('content')
@if(session('msg'))
<div class="alert alert-primary alert-dismissible" role="alert">
    {{session('msg')}}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<div class="row">
    <div class="col-md-7">
        <div class="row">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <button type="button" class="nav-link {{(old('bio') == null? 'active' : '')}}" role="tab"
                            data-bs-toggle="tab" data-bs-target="#navs-1" aria-controls="navs-1"
                            aria-selected="true">Daftar Tautan</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link {{(old('bio') != null? 'active' : '')}}" role="tab"
                            data-bs-toggle="tab" data-bs-target="#navs-2" aria-controls="navs-2" aria-selected="false"
                            tabindex="-1">Kustomisasi Situs</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade {{(old('bio') == null? 'active show' : '')}}" id="navs-1" role="tabpanel">
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="text-right mb-2">
                                    <a class="btn m-0 p-0" title="Salin URL Anda"
                                        onclick=navigator.clipboard.writeText("{{ 's.jgu.ac.id/m/'.$data->shortlink }}")><i
                                            class="bx bx-copy"></i></a>
                                    <span class="text-muted">s.jgu.ac.id/</span><b>m/{{$data->shortlink}}</b>
                                    <a class="btn m-0 p-0" title="Cetak" target="_blank"
                                        href="{{ url('MICROSITE/print/') }}/{{Crypt::encrypt($data->id)}}"><i
                                            class="bx bx-printer"></i></a>
                                    <span class="text-muted">Cetak</b>
                                        <br>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group mb-3">
                                        <input type="text"
                                            class="form-control @error('judul_tautan') is-invalid @enderror"
                                            name="judul_tautan" id="judul_tautan" placeholder="Judul Tautan">
                                        @error('judul_tautan')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="input-group mb-3">
                                        <input type="hidden" name="type_form" value="add">
                                        <input type="hidden" name="microsite_id" value="{{$data->id}}">
                                        <input type="url" class="form-control @error('tautan') is-invalid @enderror"
                                            name="tautan" id="tautan" placeholder="Tautan ( https://... )">
                                        <button class="btn btn-outline-primary" type="submit">Tambahkan!</button>
                                        @error('tautan')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="card-datatable table-responsive">
                                    <table class="table table-hover table-sm" id="datatable" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="20px" data-priority="1">No</th>
                                                <th data-priority="2">Judul</th>
                                                <th>Tautan</th>
                                                <th width="85px" data-priority="3">Aksi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade {{(old('bio') != null? 'active show' : '')}}" id="navs-2" role="tabpanel">
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Judul <i class="text-danger">*</i></label>
                                    <div class="input-group">
                                        <input type="text" name="judul"
                                            class="form-control @error('judul') is-invalid @enderror"
                                            value="{{ (old('judul') != null ? old('judul') : $data->title) }}">
                                        @error('judul')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Shortlink <i class="text-danger">*</i></label>
                                    <div class="input-group">
                                        <span class="input-group-text @error('shortlink') btn-danger @enderror">
                                            s.jgu.ac.id/m/
                                        </span>
                                        <input type="text" name="shortlink"
                                            class="form-control @error('shortlink') is-invalid @enderror"
                                            value="{{ (old('shortlink') != null ? old('shortlink') : $data->shortlink) }}"
                                            placeholder="something">
                                        @error('shortlink')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Avatar/Logo</label>
                                    <div class="input-group">
                                        <input class="form-control @error('avatar') is-invalid @enderror" name="avatar"
                                            type="file" accept=".jpg, .jpeg, .png" title="JPG/PNG">
                                        @error('avatar')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-md-12">
                                    <label for="link" class="form-label">Bio <i class="text-danger">*</i> <small><i>support tag html</i></small></label>
                                    <textarea name="bio" class="form-control @error('bio') is-invalid @enderror">{{ (old('bio') != null ? old('bio') : $data->bio) }}</textarea>
                                    @error('bio')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="mt-2">
                                    <input type="hidden" name="type_form" value="edit">
                                    <button type="submit" name="ubah" class="btn btn-primary me-2">Simpan</button>
                                    <a class="btn btn-outline-secondary"
                                        href="{{ route('MICROSITE.index') }}">Kembali</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <iframe src="{{ route('MICROSITE.view', ['id' => $data->shortlink]) }}" id="iframe_view" width="100%"
            style="height: 75vh;">
        </iframe>
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
                url: "{{ route('MICROSITE.links',['id' => $data->id]) }}",
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
                        return row.title;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return `<a class="text-primary" target="_blank" href="` + row.link +
                            `">` + row.link + `</a>`;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return `<a class="text-primary" title="edit title" style="cursor:pointer" onclick="EditTitleId(` +
                            row.id +
                            `)" ><i class="bx bxs-pencil"></i></a> <a class="text-success" title="edit link" style="cursor:pointer" onclick="EditId(` +
                            row.id +
                            `)" ><i class="bx bxs-edit-alt"></i></a> <a class="text-danger" title="Hapus" style="cursor:pointer" onclick="DeleteId(` +
                            row.id +
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
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('MICROSITE.delete_link') }}",
                        type: "DELETE",
                        data: {
                            "id": id,
                            "microsite_id": "{{$data->id}}",
                            "_token": $("meta[name='csrf-token']").attr("content"),
                        },
                        success: function (data) {
                            if (data['success']) {
                                swal(data['message'], {
                                    icon: "success",
                                });
                                location.reload();
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

    function EditId(id) {
        swal({
                text: 'Silahkan masukkan tautan pengganti (http://...)',
                content: "input",
                button: {
                    text: "Simpan",
                },
            })
            .then(name => {
                if (!name) {
                    swal('Tidak boleh Kosong!', {
                        icon: "error",
                    });
                } else {
                    $.ajax({
                        url: "{{ route('MICROSITE.edit_link') }}",
                        type: "POST",
                        data: {
                            "id": id,
                            "microsite_id": "{{$data->id}}",
                            "link": name,
                            "_token": $("meta[name='csrf-token']").attr("content"),
                        },
                        success: function (data) {
                            if (data['success']) {
                                swal(data['message'], {
                                    icon: "success",
                                });
                                location.reload();
                            } else {
                                swal(data['message'], {
                                    icon: "error",
                                });
                            }
                        }
                    })
                };
            })
    }

    function EditTitleId(id) {
        swal({
                text: 'Silahkan masukkan judul pengganti',
                content: "input",
                button: {
                    text: "Simpan",
                },
            })
            .then(name => {
                if (!name) {
                    swal('Tidak boleh Kosong!', {
                        icon: "error",
                    });
                } else {
                    $.ajax({
                        url: "{{ route('MICROSITE.edit_title') }}",
                        type: "POST",
                        data: {
                            "id": id,
                            "microsite_id": "{{$data->id}}",
                            "title": name,
                            "_token": $("meta[name='csrf-token']").attr("content"),
                        },
                        success: function (data) {
                            if (data['success']) {
                                swal(data['message'], {
                                    icon: "success",
                                });
                                location.reload();
                            } else {
                                swal(data['message'], {
                                    icon: "error",
                                });
                            }
                        }
                    })
                };
            })
    }

</script>
@endsection
