@extends('layouts.master')
@section('title', 'Lengkapi Profil')
@section('breadcrumb-items')
<span class="text-muted fw-light">Akun Portal Wifi / </span>
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
            <h5 class="card-header">Lengkapi Profil</h5>
            <!-- Account -->
            <hr class="my-0">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="row">
                        @csrf
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Nama <small class="text-danger">*</small></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                value="{{ Auth::user()->name }}" placeholder="Nama Lengkap" autofocus />
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Username <small class="text-danger">*</small></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                name="username" value="{{ Auth::user()->username }}" placeholder="ID Staff/NIM"
                                @if(Auth::user()->username != null ) readonly title="Silahkan hubungi Admin" @endif />
                            @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                            @if(Auth::user()->username == null)
                            <span class="text-danger">
                                <strong>Isi dengan Username/ID Staff/Matrix/NIM</strong>
                            </span>
                            @endif
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email <small class="text-danger">*</small></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email"
                                value="{{ (old('email') == null ? Auth::user()->email : old('email')) }}" />
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="phone" class="form-label">No HP. <small class="text-danger">*</small></label>
                            <input type="number" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" maxlength="15"
                                value="{{ (old('phone') == null ? Auth::user()->phone : old('phone')) }}" />
                            @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Jabatan/Pekerjaan <small class="text-danger">*</small></label>
                            <input type="text" class="form-control @error('job') is-invalid @enderror" name="job"
                                value="{{ (old('job') == null ? Auth::user()->job : old('job')) }}" placeholder="Dosen/Staf/Mahasiswa/Tamu dll."/>
                            @error('job')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Jenis Kelamin <small class="text-danger">*</small></label>
                            <select class="select2 form-select col-sm-12 @error('gender') is-invalid @enderror"
                                name="gender">
                                <option value="" {{ (Auth::user()->gender == null ? 'selected' : '') }} disabled>
                                    Select</option>
                                @foreach($gender as $g)
                                <option value="{{ $g['id'] }}" {{ ((Auth::user()->gender == $g['id']) ? 'selected' : ''); }}> {{$g['title']}}</option>
                                @endforeach
                            </select>
                            @error('gender')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Simpan</button>
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
