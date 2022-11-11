@extends('layouts.master')
@section('title', $data->title )

@section('breadcrumb-items')
<span class="text-muted fw-light">Absensi / Acara /</span>
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
        max-width: 100px;
    }

    table.dataTable td:nth-child(4) {
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
                            <div class="col-md-6 text-md-start text-center pt-3 pt-md-0">
                                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"><i
                                        class="bx bx-chevron-left me-sm-2"></i>
                                    <span>Kembali</span>
                                </a>
                            </div>
                            <div class="col-md-6 text-md-end text-center pt-3 pt-md-0">
                                <a href="{{ route('att.print', ['id' => Crypt::encrypt($data->id) ]) }}" target="_blank"
                                    class="btn btn-primary"><i class="bx bx-qr-scan me-sm-2"></i>
                                    <span>Qr-Code</span>
                                </a>
                                <button class="btn btn-primary" type="submit"><i class="bx bx-printer me-sm-2"></i>
                                    <span>Cetak Laporan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-hover table-sm" id="datatable" width="100%">
            <thead>
                <tr>
                    <th width="20px" data-priority="1">No</th>
                    <th data-priority="2">Nama</th>
                    <th>Jabatan</th>
                    <th>Lokasi Absensi</th>
                    <th data-priority="4" width="150px">Waktu Kehadiran</th>
                    @if($data->user_id == Auth::user()->id)
                    <th data-priority="3" width="50px">Aksi</th>
                    @endif
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
                searchPlaceholder: 'Cari..',
                url: "{{asset('assets/vendor/libs/datatables/id.json')}}"
            },
            ajax: {
                url: "{{ route('att.list_data', ['id' => $id]) }}",
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
                        if (row.user != null) {
                            return "<span title='" + row.user.name + "'>" + row.user
                                .name_with_title +
                                "</span>";
                        } else {
                            return "<span class='text-danger' title='unregistered user'>" + row
                                .username +
                                "</span>";
                        }
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        if (row.user != null) {
                            return "<span title='" + row.user.job + "'>" + row.user.job +
                                "</span>";
                        }
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        if (row.longitude != null) {
                            return "<a target='_blank' href='https://www.google.com/maps?q=" +
                                row.latitude + "," + row.longitude +
                                "' title='Klik untuk melihat lokasi absensi'>" + row.latitude +
                                " , " + row.longitude +
                                "</a>";
                        }
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return "<span title='" + row.created_at + "'>" + row.date +
                            "</span>";
                    },
                },
                @if($data->user_id == Auth::user()->id)
                {
                    render: function (data, type, row, meta) {
                        return `<a class="text-danger" title="Delete" style="cursor:pointer" onclick="DeleteId(` + row
                            .id +
                            `)" ><i class="bx bx-trash"></i></a> `;
                    },
                    className: "text-center"
                }
                @endif
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
                        url: "{{ route('att.list_delete') }}",
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
