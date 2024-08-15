@extends('layouts.master')
@section('title', 'Akun Portal Wifi')

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

    table.dataTable td:nth-child(2) {
        max-width: 150px;
    }

    table.dataTable td:nth-child(4) {
        max-width: 90px;
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
                            <div class="offset-md-9 col-md-3 text-md-end text-center pt-3 pt-md-0">
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
                    <h5 id="offcanvasEndLabel" class="offcanvas-title">Tambah Pengguna Wifi</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body my-auto mx-0 flex-grow-1">
                    <form class="add-new-record pt-0 row g-2 fv-plugins-bootstrap5 fv-plugins-framework" id="form-add-new-record" method="POST" action="">
                        @csrf
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Username / NIK / NIM <small class="text-danger">*</small></label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" id="nik"
                                    placeholder="Nomor Induk Karyawan / NIM" value="{{ old('username') }}">
                                @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Nama <small class="text-danger">*</small></label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" id="nama" onkeyup="createPassword()" 
                                    placeholder="Nama" value="{{ old('nama') }}" maxlength="24">
                                @error('nama')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Email</label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                    placeholder="Email" value="{{ old('email') }}" maxlength="24">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label" for="basicDate">Password Wifi <small class="text-danger">*</small></label>
                            <div class="input-group input-group-merge has-validation">
                                <input type="text" class="form-control @error('password') is-invalid @enderror" name="password" id="password"
                                    placeholder="Password Portal Wifi" value="{{ old('password') }}" maxlength="24">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-12 fv-plugins-icon-container">
                            <label class="form-label">Wifi Group <small class="text-danger">*</small></label>
                            <div class="input-group input-group-merge has-validation">
                            <select class="form-select @error('wifi_group') is-invalid @enderror select2-modal" data-placeholder="-- Pilih Group --"
                                name="wifi_group">
                                @foreach($wifi_group as $g)
                                <option value="{{ $g }}"> {{ $g }}</option>
                                @endforeach
                            </select>
                            @error('wifi_group')
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
                            <strong>Setelah menambahkan disini,<br>maka akan diregisterkan otomatis ke server radius</strong>
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
                    <th data-priority="2">Nama</th>
                    <th width="60px">Username</th>
                    <th width="150px">Password</th>
                    <th width="150px">Group</th>
                    <th width="130px" data-priority="3">Terakhir Dilihat</th>
                    @if (Auth::user()->id == 1)
                    <th width="40px" >Aksi</th>
                    @endif
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

    function createPassword(){
        var x = $('#nama').val();
        x = x.replace(/[^A-Z0-9]+/ig, "");
        x = x.toUpperCase();
        var init = x.substring(0, 5);
        if(init.length < 5){
            init = init.padEnd(5, 'X')
        }
        init = init + "" + Math.floor(100 + Math.random() * 900);
        document.getElementById("password").value = init;
    }

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
                url: "{{ route('setting_account_wifi_data') }}",
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
                    className: "text-center",
                    "orderable": false
                },
                {
                    render: function (data, type, row, meta) {
                        var html = `<a class="text-primary" title="` + row.name +
                            `" href="{{ url('setting/account_wifi/` +
                            row.username + `') }}">` + row.name + `</a>`;
                        return html;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        var html = `<code>` + row.username + `</code>`;
                        return html;
                    },
                    className: "text-md-center",
                },
                {
                    render: function (data, type, row, meta) {
                        var html = `<code>` + row.password + `</code>`;
                        return html;
                    },
                    className: "text-md-center"
                },

                {
                    render: function (data, type, row, meta) {
                        var html = `<code>` + row.wifi_group + `</code>`;
                        return html;
                    },
                    className: "text-md-center"
                },
                {
                    render: function (data, type, row, meta) {
                        if(row.is_seen == 1){
                            return '<i class="bx bx-wifi text-success"></i> <small>' + moment(row.updated_at).format('D/M/YYYY HH:mm') + '</small>';
                        } else {
                            return '<i class="bx bx-low-vision text-light"></i>';
                        }
                    },
                    className: "text-center"
                },
                @if (Auth::user()->id == 1)
                {
                    render: function (data, type, row, meta) {
                        var html = ` <a class=" text-danger" title="Hapus" style="cursor:pointer" onclick="DeleteId(` + row
                            .id + `)" ><i class="bx bx-trash"></i></a>`;
                        return html;
                    },
                    "orderable": false,
                    className: "text-md-center"
                }
                @endif
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
                        url: "{{ route('setting_account_wifi_delete') }}",
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
