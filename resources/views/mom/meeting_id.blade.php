@extends('layouts.master')
@section('title', $activity->title)

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/swiper/swiper.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/ui-carousel.css')}}" />
@endsection

@section('style')
<style>
    td {
        vertical-align: top;
        word-wrap: break-word;
        padding: 2px;
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
                <div class="swiper gallery-top">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide"
                            style="background-image:url(https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/img/backgrounds/6.jpg)">
                            Dokumentasi 1</div>
                        <div class="swiper-slide"
                            style="background-image:url(https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/img/backgrounds/5.jpg)">
                            Dokumentasi 2</div>
                        <div class="swiper-slide"
                            style="background-image:url(https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/img/backgrounds/7.jpg)">
                            Dokumentasi 3</div>
                    </div>
                    <!-- Add Arrows -->
                    <div class="swiper-button-next swiper-button-white"></div>
                    <div class="swiper-button-prev swiper-button-white"></div>
                </div>
            </div>
            <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                <div class="flex-grow-1 mt-4">
                    <div
                        class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                        <div class="user-profile-info">
                            <h4>{{ strtoupper($activity->title) }}</h4>
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
                        <a onclick="window.print()" class="btn btn-light m-0 mt-2 p-0 text-info d-none d-md-block"
                            title="Cetak halaman ini">
                            <i class="bx bx-printer"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">

    </div>
</div>
<!--/ Header -->
<div class="row">
    <!-- Details Data -->
    <div class="col-12 mb-md-0 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row table-responsive">
                    <table class="table">
                        <tr>
                            <th style="width:50px">No.</th>
                            <th>Uraian Rapat</th>
                            <th width="150px">PIC</th>
                            <th width="120px">Target</th>
                        </tr>
                        @foreach($lists as $key => $d)
                        <tr>
                            <td class="text-center">{{ $key+1 }}</td>
                            <td>{!! $d->detail !!}</td>
                            <td>
                                @foreach($d->pics as $key => $p)
                                {{ ucwords(strtolower($p->name)) }},
                                @endforeach
                            </td>
                            <td>{{ $d->target }}</td>
                        </tr>
                        @endforeach
                    </table>
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

<script src="{{asset('assets/vendor/libs/swiper/swiper.js')}}"></script>
<script src="{{asset('assets/js/ui-carousel.js')}}"></script>
@endsection
