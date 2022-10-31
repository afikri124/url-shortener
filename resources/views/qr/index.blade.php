@extends('layouts.master')
@section('title', 'Pembuat QR-Code')

@section('breadcrumb-items')
<!-- <span class="text-muted fw-light"> /</span> -->
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/sweetalert2.css')}}">
@endsection

@section('style')
@endsection

@section('content')
<div class="card mb-4">
    <form class="row p-3">
        <div class="col-md-6">
            <label class="form-label">Data <i class="text-danger">*</i></label>
            <div class="input-group mb-3">
                <input type="text" name="data" id="data" placeholder="Masukkan URL/Email/Phone/Text" class="form-control" >
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Label</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="label" placeholder="Label dibawah QR-Code (boleh dikosongkan)"  id="label">
                <button class="btn btn-outline-primary" type="button" onclick="GenerateQR()" id="button-addon2">Buat Sekarang!</button>
            </div>
        </div>
    </form>
</div>
<div class="text-center mt-5">
    <div id="qrcode"><h3 class="text-light">Silahkan masukkan data yang ingin anda konversi ke QR-Code terlebih dahulu!</h3></div>
</div>
@endsection

@section('script')
<script src="{{asset('assets/js/sweetalert.min.js')}}"></script>
<script type="text/javascript">
    function GenerateQR() {
        if($('#data').val() == ""){
            swal("Data tidak boleh kosong!", { icon: "error",});
            document.getElementById("qrcode").innerHTML = "";
        } else {
            document.getElementById("qrcode").innerHTML = "<img src='{{ url('qrcode')}}?data=" + $('#data').val() + "&label="+ $('#label').val() + "'>";
        }
    }

</script>
@endsection
