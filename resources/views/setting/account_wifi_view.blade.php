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
                        <span>{{ $wifiuser->password }}</span></li>
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
                <small class="text-muted text-uppercase">Data di Server Radius</small>
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
                            class="fw-semibold mx-2">Value:</span>
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
