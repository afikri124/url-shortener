@extends('layouts.master')
@section('title', $data->activity->title)

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('style')
<style>
    td {
        vertical-align: top;
        word-wrap: break-word;
    }

</style>
@endsection

@section('breadcrumb-items')
<span class="text-muted fw-light">Notulensi / Risalah Rapat / </span>
@endsection


@section('content')
<div class="row invoice-preview">
    <!-- Details Data -->
    <div class="col-12 mb-md-0 mb-4">
        <div class="card invoice-preview-card">
            <div class="card-body">
                <div class="row">
                    <div class="mb-xl-0 mb-4 col-md-7">
                        <div class="d-flex svg-illustration mb-3 gap-2">
                            <span class="app-brand-logo demo">
                                <img src="" class="d-block h-auto ms-0 rounded user-profile-img" width="100px">
                            </span>
                        </div>
                        <h3 class="mb-1">{{ $data->activity->title }}</h3>
                    </div>
                    <div class=" col-md-5">
                        <table width="100%">
                            <tr>
                                <td>
                                    <div class="mb-2">
                                        <span class="me-1"><i class='bx bx-id-card'></i>
                                            {{ $data->activity->type }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="me-1"><i class='bx bx-mail-send'></i>
                                            {{ $data->activity->date }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="me-1"><i class='bx bx-briefcase'></i>
                                            {{ $data->activity->location }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="me-1"><i class='bx bx-certification'></i>
                                            {{ $data->activity->host }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="me-1"><i class='bx bx-flag'></i>
                                            {{ $data->activity->participant }}</span>
                                    </div>
                                </td>
                                <td align="center" style="vertical-align: middle;">

                                    <img src="https://s.jgu.ac.id/qrcode?data=" style="height: 85px;"><br>
                                    <a href="" target="_blank" class="btn btn-light m-0 mt-2 p-0 text-info"
                                        title="Cetak halaman ini"><i class="bx bx-printer"></i>
                                        Print</a>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
            </div>
            <hr class="my-0">
            <div class="card-body">
                <div class="row">
                    <div>
                        <table>
                            <tr>
                                <td><strong>PIC</strong></td>
                                <td>
                                    @foreach($data->pics as $x)
                                    <i class="badge bg-dark m-0">{{ $x->name }}</i>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Target</strong></td>
                                <td>{{ $data->target }}</td>
                            </tr>
                            <tr>
                                <td><strong>Risalah Rapat</strong></td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        {!! $data->detail !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Details Data -->
</div>
@endsection
@section('script')
@endsection
