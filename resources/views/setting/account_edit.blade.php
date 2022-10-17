@extends('layouts.master')
@section('title', $data->nama )
@section('breadcrumb-items')
<span class="text-muted fw-light">Setting / Account / </span>
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('style')
<style>

</style>
@endsection

@section('content')
<!-- User Profile Content -->
<div class="row">
    <div class="col-md-12">
        @if(session('msg'))
        <div class="alert alert-primary alert-dismissible" role="alert">
            {{session('msg')}}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="card mb-4">
            <h5 class="card-header"><img src="{{ $data->image() }}" class="w-40 h-40 rounded-circle" style="width:40px; height:40px;object-fit: cover; margin-right:10px;"> Perbarui Akun </h5>
            <!-- Account -->
            <hr class="my-0">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="row">
                        @csrf
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ $data->name }}" placeholder="Enter Name" autofocus />
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="user" class="form-label">Username</label>
                            <input type="username" class="form-control @error('username') is-invalid @enderror"
                                id="user" name="username" value="{{ $data->username  }}"
                                placeholder="Masukkan Username" />
                            @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ (old('email') == null ? 
                                    ( strstr($data->email, $data->username) == false ? $data->email : '') 
                                    : old('email')) }}" placeholder="Masukkan Email"  />
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="job" class="form-label">Job</label>
                            <select class="select2 form-select col-sm-12 @error('job') is-invalid @enderror"
                                name="jabatan" id="jabatan">
                                <option value=""
                                    {{ (old('jabatan') == null  ? 'selected' : ($data->jabatan == null ? 'selected' : '' ))}}
                                    disabled>Jabatan
                                    Akademik</option>
                                <option
                                    {{ (old('jabatan') == "Dosen"  ? 'selected' : ($data->jabatan == "Dosen" ? 'selected' : '')) }}>
                                    Dosen</option>
                                <option
                                    {{ (old('jabatan') == "Ketua Program Studi"  ? 'selected' : ($data->jabatan == "Ketua Program Studi" ? 'selected' : '')) }}>
                                    Ketua Program Studi</option>
                                <option
                                    {{ (old('jabatan') == "Dekan"  ? 'selected' : ($data->jabatan == "Dekan" ? 'selected' : '' ))}}>
                                    Dekan</option>
                            </select>
                            @error('jabatan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="unit_kerja" class="form-label">Unit Kerja</label>
                            <select class="select2 form-select col-sm-12 @error('unit_kerja') is-invalid @enderror"
                                name="unit_kerja" id="unit_kerja">
                                <option value=""
                                    {{ (old('unit_kerja') == null  ? 'selected' : ($data->unit_kerja == null ? 'selected' : '')) }}
                                    disabled>Unit Kerja</option>
                                <option
                                    {{ (old('unit_kerja') == "Teknik Sipil"  ? 'selected' : ($data->unit_kerja == "Teknik Sipil" ? 'selected' : '')) }}>
                                    Teknik Sipil
                                </option>
                                <option
                                    {{ (old('unit_kerja') == "Teknik Mesin"  ? 'selected' : ($data->unit_kerja == "Teknik Mesin" ? 'selected' : '')) }}>
                                    Teknik Mesin
                                </option>
                                <option
                                    {{ (old('unit_kerja') == "Teknik Elektro"  ? 'selected' : ($data->unit_kerja == "Teknik Elektro" ? 'selected' : '')) }}>
                                    Teknik Elektro</option>
                                <option
                                    {{ (old('unit_kerja') == "Teknik Informatika"  ? 'selected' : ($data->unit_kerja == "Teknik Informatika" ? 'selected' : '')) }}>
                                    Teknik Informatika</option>
                                <option
                                    {{ (old('unit_kerja') == "Teknik Industri"  ? 'selected' : ($data->unit_kerja == "Teknik Industri" ? 'selected' : '')) }}>
                                    Teknik Industri</option>
                                <option
                                    {{ (old('unit_kerja') == "Farmasi"  ? 'selected' : ($data->unit_kerja == "Farmasi" ? 'selected' : '')) }}>
                                    Farmasi</option>
                                <option
                                    {{ (old('unit_kerja') == "Akuntansi"  ? 'selected' : ($data->unit_kerja == "Akuntansi" ? 'selected' : '')) }}>
                                    Akuntansi
                                </option>
                                <option
                                    {{ (old('unit_kerja') == "Manajemen"  ? 'selected' : ($data->unit_kerja == "Manajemen" ? 'selected' : '')) }}>
                                    Manajemen
                                </option>
                                <option
                                    {{ (old('unit_kerja') == "Bisnis Digital"  ? 'selected' : ($data->unit_kerja == "Bisnis Digital" ? 'selected' : '')) }}>
                                    Bisnis Digital</option>
                                <option
                                    {{ (old('unit_kerja') == "S2 Teknik Elektro"  ? 'selected' : ($data->unit_kerja == "S2 Teknik Elektro" ? 'selected' : '')) }}>
                                    S2 Teknik Elektro</option>
                            </select>
                            @error('unit_kerja')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="fakultas" class="form-label">Fakultas</label>
                            <select class="select2 form-select col-sm-12 @error('fakultas') is-invalid @enderror"
                                name="fakultas" id="fakultas">
                                <option value="" {{ $data->fakultas == null ? 'selected' : '' }} disabled>
                                    Fakultas</option>
                                <option
                                    {{ (old('fakultas') == "Teknik dan Ilmu Komputer"  ? 'selected' : ($data->fakultas == "Teknik dan Ilmu Komputer" ? 'selected' : '')) }}>
                                    Teknik dan Ilmu Komputer</option>
                                <option
                                    {{ (old('fakultas') == "Ekonomi dan Bisnis"  ? 'selected' : ($data->fakultas == "Ekonomi dan Bisnis" ? 'selected' : '')) }}>
                                    Ekonomi dan Bisnis</option>
                                <option
                                    {{ (old('fakultas') == "Farmasi"  ? 'selected' : ($data->fakultas == "Farmasi" ? 'selected' : '')) }}>
                                    Farmasi</option>
                            </select>
                            @error('fakultas')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Hak Akses</label>
                            <select class="select2 form-select" multiple="multiple" name="roles[]" id="select2Dark"
                                >
                                @foreach($roles as $role)
                                <option value="{{$role->id}}" {{ $data->hasRole($role->id) ? 'selected' : '' }}>
                                    {{$role->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Simpan</button>
                        <a class="btn btn-outline-secondary" href="{{ route('pengaturan.akun') }}">Kembali</a>
                    </div>
                </form>
            </div>
            <!-- /Account -->
        </div>
    </div>
</div>

<!--/ User Profile Content -->
@endsection
@section('script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script>
    "use strict";
    $(function () {
        const e = $(".selectpicker"),
            t = $(".select2"),
            c = $(".select2-icons");

        function i(e) {
            return e.id ? "<i class='bx bxl-" + $(e.element).data("icon") + " me-2'></i>" + e.text : e.text
        }
        e.length && e.selectpicker(), t.length && t.each(function () {
            var e = $(this);
            e.wrap('<div class="position-relative"></div>').select2({
                placeholder: "Select value",
                dropdownParent: e.parent()
            })
        }), c.length && c.wrap('<div class="position-relative"></div>').select2({
            templateResult: i,
            templateSelection: i,
            escapeMarkup: function (e) {
                return e
            }
        })
    });

</script>
@endsection
