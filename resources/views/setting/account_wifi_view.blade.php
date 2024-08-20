@extends('layouts.master')
@section('title', $wifiuser->first_name)
@section('breadcrumb-items')
<span class="text-muted fw-light">Pengaturan / Akun Portal Wifi / </span>
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
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

    table.dataTable td {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

</style>
@endsection

@section('content')
<!-- Header -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                    <img src="{{ $photo }}"
                        class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" width="100px">
                </div>
                <div class="flex-grow-1 mt-4">
                    <div
                        class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                        <div class="user-profile-info">
                            <h4>{{ $wifiuser->first_name }} {{ $wifiuser->last_name }}</h4>
                            <small class="text-muted">{{ $wifiuser->username }}</small>
                        </div>
                        <a href="" class="btn btn-primary text-nowrap">
                            <i class='bx bx-user-check'></i> {{ $wifiuser->username }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Header -->
<!-- User Profile Content -->
<div class="row">
    <div class="col-md-4"> 
        <!-- Projects table -->
        <div class="card mb-4">
            <div class="card-body">
                <small class="text-muted text-uppercase">Data Wifi</small>
                <ul class="list-unstyled mb-4 mt-3">
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-user"></i><span
                            class="fw-semibold mx-2">Nama:</span>
                        <span>{{ $wifiuser->first_name }} {{ $wifiuser->last_name }}</span></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-user-check"></i><span
                            class="fw-semibold mx-2">Username:</span>
                        <span>{{ $wifiuser->username }}</span></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-key"></i><span
                            class="fw-semibold mx-2">Password:</span>
                        <code class="text-black">{{ $wifiuser->password }}</code></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-mail-send"></i><span
                            class="fw-semibold mx-2">Email:</span>
                        <span>{{ $wifiuser->email }}</span></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-low-vision"></i><span
                            class="fw-semibold mx-2">Lihat Password:</span>
                        <span>
                            @if ($wifiuser->is_seen)
                            Sudah
                            @else
                            Belum
                            @endif
                        </span></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-group"></i><span
                            class="fw-semibold mx-2">Group:</span>
                        <span>{{ $wifiuser->wifi_group }}</span></li>
                </ul>
            </div>
        </div>
        <!--/ Projects table -->
    </div>
    <div class="col-md-4">
        <!-- Projects table -->
        <div class="card mb-4">
            <div class="card-body">
                <small class="text-muted text-uppercase">Data Server Radius</small>
                @if ($radius == null )
                <div class="alert alert-info">User tidak terdaftar di server radius!</div>
                @elseif ($radius == "ERROR")
                <div class="alert alert-danger">SERVER RADIUS ERROR !</div>
                @else
                <ul class="list-unstyled mb-4 mt-3">
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-id-card"></i><span
                            class="fw-semibold mx-2">Username:</span>
                        <span>{{ $radius->username }}</span></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-lock-open-alt"></i><span
                            class="fw-semibold mx-2">Password:</span>
                        <span>{{ $radius->value }}</span></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-group"></i><span
                            class="fw-semibold mx-2">Group:</span>
                        <span>
                            @foreach($group as $g)
                            {{ $g->groupname }}<br>
                            @endforeach
                        </span></li>
                </ul>
                @endif
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <small class="text-muted text-uppercase">Data Mesin Absen</small>
                @if ($absen == null )
                <div class="alert alert-info">User tidak terdaftar di mesin absen!</div>
                @else
                <ul class="list-unstyled mb-4 mt-3">
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-id-card"></i><span
                            class="fw-semibold mx-2">Username:</span>
                        <span>{{ ($absen->username == null ? $absen->username_old : $absen->username) }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-user"></i><span
                            class="fw-semibold mx-2">Nama di Mesin:</span>
                        <span>{{ $absen->name }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-group"></i><span
                            class="fw-semibold mx-2">Group:</span>
                            <span>{{ $absen->group->title }} {{ $absen->group->desc }}</span></li>
                    </li>
                </ul>
                @endif
            </div>
        </div>
        <!--/ Projects table -->
    </div>
    <div class="col-md-4">
        <!-- About User -->
        <div class="card mb-4">
            <div class="card-body">
                <small class="text-muted text-uppercase">Data Akun S.JGU</small>
                @if ($user == null)
                <div class="alert alert-info">Tidak terdaftar di Akun S.JGU</div>
                @else
                <ul class="list-unstyled mb-4 mt-3">
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-user"></i><span
                            class="fw-semibold mx-2">Nama:</span>
                        <span>{{ $user->name }}</span></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-user-check"></i><span
                            class="fw-semibold mx-2">Username:</span>
                        <span>{{ $user->username }}</span></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-mail-send"></i><span
                            class="fw-semibold mx-2">Email:</span>
                        <span>{{ $user->email }}</span></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-phone"></i><span
                            class="fw-semibold mx-2">No HP:</span>
                        <span>{{ $user->phone }}</span></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-briefcase"></i><span
                            class="fw-semibold mx-2">Jabatan:</span>
                        <span>{{ $user->job }}</span></li>
                    <li class="d-flex align-items-center mb-3"><i class="bx bx-male-sign"></i><span
                            class="fw-semibold mx-2">Jenis Kelamin:</span>
                        <span>{{ $user->getJK() }}</span></li>
                </ul>
                @endif
            </div>
        </div>
        <!--/ About User -->
        <!-- Profile Overview -->
        <div class="card mb-4">
            <div class="card-body">
                <small class="text-muted text-uppercase">Hak Akses</small><br>
                @if ($user == null)
                <div class="alert alert-info">Belum memiliki hak akses S.JGU</div>
                @else
                <ul class="list-unstyled mt-3">
                    <span>
                        @if($user->roles->count() == 0)
                        <p class="p-0 mb-0 text-danger">Anda tidak memiliki hak akses, harap hubungi administrator!</p>
                        @else
                        @foreach($user->roles as $x)
                        <i class="badge bg-{{ $x->color }} m-0">{{ $x->title }}</i>
                        @endforeach
                        @endif
                    </span>
                </ul>
                @endif
            </div>
        </div>
        <!--/ Profile Overview -->
    </div>
</div>
<!--/ User Profile Content -->
@endsection
@section('script')

@endsection
