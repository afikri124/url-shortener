@extends('layouts.master')
@section('title', 'Cuti atau Libur')

@section('breadcrumb-items')
<span class="text-muted fw-light">Pengaturan /</span>
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/sweetalert2.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />
@endsection

@section('style')
<style>
    table.dataTable tbody td {
        vertical-align: middle;
    }

    table.dataTable td:nth-child(3) {
        max-width: 150px;
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
<div class="card">
    <div class="card-datatable table-responsive">
        <div class="card-header flex-column flex-md-row pb-0">
            <div class="row">
                <div class="col-12 pt-3 pt-md-0">
                    <div class="col-12">
                        <div class="row">
                            <div class=" col-md-3">
                                <select id="select_tahun" class="select2 form-select" data-placeholder="Tahun">
                                    <option value="">Tahun</option>
                                    @foreach($tahun as $d)
                                    <option value="{{ $d->year }}">{{ $d->year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-3">
                                <select id="select_bulan" class="select2 form-select" data-placeholder="Bulan">
                                    <option value="">Bulan</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ \Carbon\Carbon::parse(\Carbon\Carbon::create()->day(1)->month($i))->format('F')}}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="offset-md-3 col-md-3 text-md-end text-center pt-3 pt-md-0">
                                <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas"
                                    data-bs-target="#newrecord" aria-controls="offcanvasEnd" tabindex="0"
                                    aria-controls="DataTables_Table_0" type="button"><span><i
                                            class="bx bx-plus me-sm-2"></i>
                                        <span>Tambah</span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="offcanvas offcanvas-end @if($errors->all()) show @endif" tabindex="-1" id="newrecord"
                aria-labelledby="offcanvasEndLabel">
                <div class="offcanvas-header">
                    <h5 id="offcanvasEndLabel" class="offcanvas-title">Tambah Hari Cuti/Libur</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body my-auto mx-0 flex-grow-1">
                    <form class="add-new-record pt-0 row g-2 fv-plugins-bootstrap5 fv-plugins-framework" id="form-add-new-record" method="POST" action="">
                        @csrf
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Tanggal</label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror" name="tanggal"
                                    placeholder="yyyy-mm-dd" value="{{ old('tanggal') }}">
                                @error('tanggal')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Nama Hari / Detail</label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" id="nama" 
                                    placeholder="Nama Hari Libur/Cuti" value="{{ old('nama') }}">
                                @error('nama')
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
                        <br>
                        <span class="invalid-feedback" role="alert"><br>
                            <strong>Setelah ditambahkan maka karyawan akan dihitung masuk meskipun tidak absen pada tanggal tersebut!</strong>
                        </span>
                        <div></div><input type="hidden">
                    </form>

                </div>
            </div>
        </div>
        <table class="table table-hover table-sm" id="datatable" width="100%">
            <thead>
                <tr>
                    <th width="10px" data-priority="1">No</th>
                    <th data-priority="2" width="60px">Tanggal</th>
                    <th >Nama Cuti/Libur</th>
                    <th width="40px" >Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables.responsive.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/responsive.bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables.checkboxes.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables-buttons.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/buttons.bootstrap5.js')}}"></script>
<script src="{{asset('assets/js/sweetalert.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
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
                searchPlaceholder: 'Cari..',
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            ajax: {
                url: "{{ route('setting_public_holiday_data') }}",
                data: function (d) {
                    d.select_tahun = $('#select_tahun').val(),
                    d.select_bulan = $('#select_bulan').val(),
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
                    className: "text-center",
                    "orderable": false
                },
                {
                    render: function (data, type, row, meta) {
                        var html = `<code>` + row.date + `</code>`;
                        return html;
                    },
                    className: "text-md-center",
                },
                {
                    render: function (data, type, row, meta) {
                        var html = row.detail;
                        return html;
                    }
                },
                {
                    render: function (data, type, row, meta) {
                        var html =
                            `<a class=" text-success" title="Edit" href="{{ url('setting/public_holiday/edit/` +
                            row.idd + `') }}"><i class="bx bxs-edit"></i></a> <a class=" text-danger" title="Hapus" style="cursor:pointer" onclick="DeleteId(\'` + row
                            .id + `\',\'` + row.detail + `\')" ><i class="bx bx-trash"></i></a>`;
                        return html;
                    },
                    "orderable": false,
                    className: "text-md-center"
                }
            ]
        });

        $('#select_tahun').change(function () {
            table.draw();
        });
        $('#select_bulan').change(function () {
            table.draw();
        });
    });

    function DeleteId(id, data) {
        swal({
                title: "Apa kamu yakin?",
                text: "Setelah dihapus, data ("+data+") tidak dapat dipulihkan!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('setting_public_holiday_delete') }}",
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
