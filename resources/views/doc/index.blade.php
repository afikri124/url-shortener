@extends('layouts.master')
@section('title', 'Unggah Bukti')

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
    table.dataTable td:nth-child(2) {
        max-width: 200px;
    }
    
    table.dataTable td:nth-child(4) {
        max-width: 130px;
    }

    table.dataTable td:nth-child(5) {
        max-width: 80px;
    }

    table.dataTable td:nth-child(6) {
        max-width: 100px;
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
<div class="alert alert-secondary alert-dismissible" role="alert">
    Silahkan login menggunakan email departemen/akun PENANGGUNG JAWAB untuk mengunggah dan mengubah status dokumen yang diperlukan. 
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
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
                        <form method="POST" class="row" target="_blank" action="">
                            @csrf
                            <div class=" col-md-3 mb-3">
                                <select id="select_aktivitas" class="select2 form-select" name="aktivitas" data-placeholder="Aktivitas">
                                    <option value="">Aktivitas</option>
                                    @foreach($activity as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-3 mb-3">
                                <select id="select_kategori" class="select2 form-select" name="kategori" data-placeholder="Kategori">
                                    <option value="">Kategori</option>
                                    @foreach($category as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-3 mb-3">
                                <select id="select_pj" class="select2 form-select" name="PJ" data-placeholder="Penanggung Jawab">
                                    <option value="">Penanggung Jawab</option>
                                    @foreach($user as $d)
                                    <option value="{{ $d->id }}"  @if(!Auth::user()->hasRole('DS') && $d->id == Auth::user()->id) selected @endif>{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-3 mb-3">
                                <select id="select_status" class="select2 form-select" name="status" data-placeholder="Status">
                                    <option value="">Status</option>
                                    @foreach($status as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            @if(Auth::user()->hasRole('DS'))
                            <div class="col-md-12 text-md-end text-center pt-3 pt-md-0 ">
                                <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas"
                                    data-bs-target="#newrecord" aria-controls="offcanvasEnd" tabindex="0"
                                    aria-controls="DataTables_Table_0" type="button"><span><i
                                            class="bx bx-plus me-sm-2"></i>
                                        <span>Tambah</span></span>
                                </button>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            @if(Auth::user()->hasRole('DS'))
            <div class="offcanvas offcanvas-end @if($errors->all()) show @endif" tabindex="-1" id="newrecord"
                aria-labelledby="offcanvasEndLabel">
                <div class="offcanvas-header">
                    <h5 id="offcanvasEndLabel" class="offcanvas-title">Tambah Dokumen</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body my-auto mx-0 flex-grow-1">
                    <form class="add-new-record pt-0 row g-2 fv-plugins-bootstrap5 fv-plugins-framework"
                        id="form-add-new-record" method="POST" action="">
                        @csrf
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label">Aktivitas <i class="text-danger">*</i></label>
                            <div class="input-group input-group-merge has-validation">
                                <select class="form-select @error('aktivitas') is-invalid @enderror select2-modal" name="aktivitas" id="aktivitas" data-placeholder="-- Pilih Aktivitas--">
                                    <option value="">-- Pilih Aktivitas --</option>
                                    @foreach($activity as $d)
                                    <option value="{{ $d->id }}" {{ ($d->id==old('kategori') ? "selected": "") }}>{{ $d->name }}</option>
                                    @endforeach
                                </select>
                                @error('aktivitas')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label">Kategori <i class="text-danger">*</i></label>
                            <div class="input-group input-group-merge has-validation">
                                <select class="form-select @error('kategori') is-invalid @enderror select2-modal" name="kategori" id="kategori" data-placeholder="-- Pilih Kategori--">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($category as $d)
                                    <option value="{{ $d->id }}" {{ ($d->id==old('kategori') ? "selected": "") }}>{{ $d->name }}</option>
                                    @endforeach
                                </select>
                                @error('kategori')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label">Nama <i class="text-danger">*</i></label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama"
                                    placeholder="Nama Dokumen yg diperlukan" value="{{ old('nama') }}">
                                @error('nama')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Link Unggah <i class="text-danger">*</i></label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="text" class="form-control @error('link') is-invalid @enderror" name="link"
                                    placeholder="Link Gdrive" value="{{ old('link') }}">
                                @error('link')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Batas Waktu</label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="datetime-local" class="form-control @error('batas_waktu') is-invalid @enderror" name="batas_waktu"
                                    placeholder="yyyy-mm-dd hh:mm" value="{{ old('batas_waktu') }}">
                                @error('batas_waktu')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Catatan</label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="text" class="form-control @error('catatan') is-invalid @enderror" name="catatan"
                                     value="{{ old('catatan') }}">
                                @error('catatan')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mt-4">
                            <button type="submit" class="btn btn-primary data-submit me-sm-3 me-1">Submit</button>
                            <button type="reset" class="btn btn-outline-secondary"
                                data-bs-dismiss="offcanvas">Batal</button>
                        </div>
                        <div></div><input type="hidden">
                    </form>

                </div>
            </div>
            @endif
        </div>

        <table class="table table-hover table-sm" id="datatable" width="100%">
            <thead>
                <tr>
                    <th width="20px" data-priority="1">No</th>
                    <th data-priority="2">Dokumen yg diperlukan</th>
                    <th width="100px">Batas Waktu</th>
                    <th>Penanggung Jawab</th>
                    <th data-priority="4">Status</th>
                    @if(Auth::user()->hasRole('DS'))
                    <th width="50px" data-priority="3">Aksi</th>
                    @endif
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/moment/id.js')}}"></script>
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
                        url: "{{ route('DOC.index_delete') }}",
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

    $(document).ready(function () {
        var table = $('#datatable').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ordering: false,
            language: {
                searchPlaceholder: 'Cari nama dokumen..',
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            ajax: {
                url: "{{ route('DOC.index_data') }}",
                data: function (d) {
                    d.activity_id = $('#select_aktivitas').val(),
                    d.category_id = $('#select_kategori').val(),
                    d.status_id = $('#select_status').val(),
                    d.pic_id = $('#select_pj').val(),
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
                        var x = `<span title='` + row.name + `'>` + row.name + `</span>`;
                        if (row.p_i_c != null) {
                            var check = false;
                            row.p_i_c.forEach((e) => {
                                if(e.department.email == "{{Auth::user()->email}}" || e.user.email == "{{Auth::user()->email}}"){
                                    check = true;
                                }
                            });
                            if(check){
                                x = `<a title="` + row.name + `" href="{{ url('DOC/view/` + row.idd +  `') }}">` + row.name + `</a>`;
                            }
                        }
                        return x;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return moment(row.deadline).format('L H:mm');
                    },
                    className: "text-md-center"
                },  
                {
                    render: function (data, type, row, meta) {
                        var x = '<ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">';
                        if (row.p_i_c != null) {
                            row.p_i_c.forEach((e) => {
                                x += '<li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" class="avatar avatar-xs pull-up" title="' +
                                    e.department.email + '"><i class="badge rounded-pill bg-primary"  style="font-size:8pt;">' + e.department.name +
                                    '</i></li>';

                                x += '<li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" class="avatar avatar-xs pull-up" title="' +
                                    e.user.email + '"><i class="badge rounded-pill bg-label-primary"  style="font-size:8pt;">' + e.user.name +
                                    '</i></li>';
                            });
                            return x;
                        }
                        var y = "</ul>";
                        return x + y;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        if (row.status != null) {
                            return "<span class='text-"+row.status.color+"' title='" + row.status.name + "'>" + row.status.name +
                                "</span>";
                        }
                    },
                },
                
                @if(Auth::user()->hasRole('DS'))
                {
                    render: function (data, type, row, meta) {
                        var x = "";
                        x += `<a class="text-success" title="Ubah" href="{{ url('DOC/edit/` + row.idd +  `') }}"><i class="bx bxs-edit"></i></a>`;
                        if(row.created_id == "{{Auth::user()->id}}" || row.created_id == "1"){
                            x += ` <a class="text-danger" title="Hapus" style="cursor:pointer" onclick="DeleteId(` + row
                            .id + `)" ><i class="bx bx-trash"></i></a>`;
                        }
                            
                        return x;
                    },
                    className: "text-center"
                }
                @endif
            ]
        });
        //filter data
        $('#select_aktivitas').change(function () {
            var id = this.value;
            $("#select_kategori").html('');
            table.draw();
            $.ajax({
                url: "{{ route('DOC.get_category_by_id') }}",
                type: "GET",
                data: {
                    id: id,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    console.log(result);
                    if (result.length != 0) {
                        $('#select_kategori').html(
                            '<option value="">Kategori</option>'
                        );
                        $.each(result, function (key, value) {
                            $("#select_kategori").append('<option value="' + value
                                .id + '">' + value.name + '</option>');
                        });
                    }
                }
            });
        });
        $('#select_kategori').change(function () {
            table.draw();
        });
        $('#select_status').change(function () {
            table.draw();
        });
        $('#select_pj').change(function () {
            table.draw();
        });
        //tambah dokumen
        $('#aktivitas').change(function () {
            var id = this.value;
            $("#kategori").html('');
            $.ajax({
                url: "{{ route('DOC.get_category_by_id') }}",
                type: "GET",
                data: {
                    id: id,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    if (result.length != 0) {
                        $('#kategori').html(
                            '<option value="">Kategori</option>'
                        );
                        $.each(result, function (key, value) {
                            $("#kategori").append('<option value="' + value
                                .id + '">' + value.name + '</option>');
                        });
                    }
                }
            });
        });
    });

</script>
@endsection
