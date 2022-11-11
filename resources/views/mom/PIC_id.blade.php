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
<span class="text-muted fw-light">Notulensi / Uraian Rapat / </span>
@endsection

@section('content')
<!-- Header -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="user-profile-header-banner">
                <img src="{{asset('assets/img/jgu.jpg')}}" class="rounded-top" width="100%" height="250px"
                    style="object-fit: cover;">
            </div>
            <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                <div class="flex-grow-1 mt-4">
                    <div
                        class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                        <div class="user-profile-info">
                            <h4>{{ strtoupper($data->activity->title) }}</h4>
                            <ul
                                class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                                <li class="list-inline-item fw-semibold">
                                    <i class='bx bx-calendar'></i>
                                    {{ \Carbon\Carbon::parse($data->activity->date)->translatedFormat("l, d F Y") }}
                                </li>
                                <li class="list-inline-item fw-semibold">
                                    <i class='bx bx-map-pin'></i>
                                    {{ $data->activity->location }}
                                </li>
                                <li class="list-inline-item fw-semibold">
                                    <i class='bx bx-user-voice'></i>
                                    {{ $data->activity->host }}
                                </li>
                                <li class="list-inline-item fw-semibold">
                                    <span class="me-1"><i class='bx bx-group'></i>
                                        {{ $data->activity->participant }}
                                </li>
                            </ul>
                        </div>
                        <a onclick="window.print()" class="btn btn-light m-0 mt-2 p-0 text-info d-none d-md-block"
                            title="Cetak halaman ini">
                            <i class="bx bx-printer"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Header -->
<div class="row invoice-preview">
    <!-- Details Data -->
    <div class="col-12 mb-md-0 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div>
                        <table class="table">
                            <tr>
                                <td><strong>PIC</strong></td>
                                <td>
                                    @php $pic = [];
                                    foreach($data->pics as $key => $p){
                                    array_push($pic, ucwords(strtolower($p->name)));
                                    }
                                    @endphp
                                    {{ implode(", ",$pic); }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Target</strong></td>
                                <td>{{ \Carbon\Carbon::parse($data->target)->translatedFormat("l, d F Y") }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Risalah Rapat</strong></td>
                                <td>{!! $data->detail !!}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer d-none d-md-block">
                <!-- <div class="mt-3"> -->
                    <a class="btn btn-outline-secondary" href="{{ url()->previous() }}"><i
                            class="bx bx-chevron-left me-sm-2"></i> Kembali</a>
                <!-- </div> -->
            </div>
        </div>
    </div>
    <!-- /Details Data -->
</div>
@endsection
@section('script')
@endsection
