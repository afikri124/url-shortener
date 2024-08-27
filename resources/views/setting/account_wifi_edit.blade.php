@extends('layouts.master')
@section('title', $data->username )
@section('breadcrumb-items')
<span class="text-muted fw-light">Pengaturan / Akun Portal Wifi  / </span>
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
            <h5 class="card-header">Edit Akun </h5>
            <!-- Account -->
            <hr class="my-0">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="row">
                        @csrf
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ $data->first_name }}"  maxlength="24" readonly/>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="user" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" value="{{ $data->username  }}" readonly />
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="user" class="form-label">Password Lama</label>
                            <input type="text" class="form-control "  value="{{ $data->password  }}" readonly />
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="password_baru" class="form-label">Password Baru</label>
                            <input type="text" class="form-control @error('password_baru') is-invalid @enderror"
                                id="password_baru" name="password_baru" value="{{ old('password_baru') }}" maxlength="24"
                                placeholder="Password Portal Wifi" autofocus required />
                            @error('password_baru')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Wifi Group</label>
                            <select class="select2 form-select" name="wifi_group" >
                                <option value="" selected disabled>- Select Wifi Group -</option>
                                @foreach($wifi_group as $d)
                                <option {{ $d==$data->wifi_group ? 'selected' : '' }} value="{{ $d }}">{{ $d }}</option>
                                @endforeach
                            </select>
                            @error('wifi_group')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Simpan</button>
                        <a class="btn btn-outline-secondary" href="{{ route('setting_account_wifi') }}">Kembali</a>
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
@endsection
