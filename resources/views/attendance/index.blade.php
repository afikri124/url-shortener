@extends('layouts.authentication.master')
@section('title', 'Absensi')

@section('css')
@endsection

@section('style')
<style>
    .kbw-signature {
        display: inline-block;
        border: 1px solid #a0a0a0;
        -ms-touch-action: none;
    }

    .kbw-signature-disabled {
        opacity: 0.35;
    }

    #sig canvas {
        width: 100%;
        height: auto;
    }

</style>
@endsection

@section('content')
<div class="col-12 justify-content-center">
    <div class="authentication-wrapper authentication-basic col-md-8">
        <div class="card">
            <div class="card-body">
                <!-- Logo -->
                <div class="app-brand justify-content-center mb-3">
                    <a href="{{ route('index') }}" class="app-brand-link gap-2">
                        <span class="app-brand-logo demo">
                            <img src="{{asset('assets/img/jgu.png')}}" width="150">
                        </span>
                    </a>
                </div>
                <!-- /Logo -->
                @if(session('msg'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{session('msg')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @else
                @if($check !=null)
                <div class="alert alert-success alert-dismissible" role="alert">
                    Anda sudah melakukan absen pada
                    <br>{{ \Carbon\Carbon::parse($check->created_at)->translatedFormat("l, d F Y H:i");}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @else
                <p class="my-4 text-center">Silahkan Isi Absensi Anda</p>
                @endif
                @endif

                @if($data->expired != null && $data->expired < \Carbon\Carbon::now() ) <div
                    class="alert alert-danger text-center" role="alert">
                    Anda sudah tidak bisa mengisi Absensi<br>karena
                    {{ $data->title }} {{ $data->sub_title }}
                    telah berakhir pada<br>{{ \Carbon\Carbon::parse($data->expired)->translatedFormat("l, d F Y H:i");}}
                    @else
                    <form id="formAuthentication" class="mb-3 row" action="" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap @if(Auth::user()->hasRole('GS'))
                                    <small class="text-mute">
                                        <strong class="text-danger">*</strong> <a href="{{ route('user.edit') }}"
                                            target="_blank"><i>klik disini memperbarui nama Anda</i></a>
                                    </small>
                                    @endif</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="nama"
                                    value="{{ Auth::user()->name_with_title }}" disabled />

                            </div>

                            <div class="mb-3 @if(Auth::user()->hasRole('GS')) d-none @endif">
                                <label for="user" class="form-label">Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="user" name="username" placeholder="NIK / NIM / Matrix ID"
                                    value="{{ (old('username') == null ? Auth::user()->username : old('username')) }}"
                                    @if(Auth::user()->username != null) readonly @endif />
                                @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" @if(Auth::user()->email != null) readonly @endif
                                value="{{ (old('email') == null ? Auth::user()->email : old('email')) }}" />
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="jabatan" class="form-label">Jabatan @if(Auth::user()->hasRole('GS')) & Nama
                                    Tempat Kerja @endif<strong class="text-danger">*</strong></label>
                                <input type="text" class="form-control @error('jabatan') is-invalid @enderror"
                                    id="jabatan" name="jabatan"
                                    value="{{ (old('jabatan') == null ? Auth::user()->job : old('jabatan')) }}"
                                    @if($check !=null) readonly @endif placeholder="Jabatan di Tempat Kerja" />
                                @error('jabatan')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Lokasi Anda <strong class="text-danger">*</strong></label>
                                <div id="current" style="display: none;">Initializing...</div>
                                <div id="map_canvas"
                                    style="width:100%; height: @if(Auth::user()->hasRole('GS')) 233px @else 150px @endif"
                                    class="text-danger"></div>
                                <div class="w-100 text-center text-md-start">
                                    <a id="googleMap" href="https://www.google.com/maps" target="_blank"
                                        class="btn btn-outline-secondary btn-sm" style="margin-top: 5px;">
                                        <small class="text-secondary">
                                            Lihat lokasi di Google Maps
                                        </small>
                                    </a>
                                </div>
                                <input id="longitude" name="longitude" type="hidden" style="display: none">
                                <input id="latitude" name="latitude" type="hidden" style="display: none"><br>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Judul @if($data->type == "M") Rapat @else Acara @endif
                                </label>
                                <input type="text" class="form-control" name="judul"
                                    value="{{ $data->title }} {{ $data->sub_title }}" readonly />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Hari/Tanggal</label>
                                <input type="text" class="form-control" name="tanggal"
                                    value='{{ \Carbon\Carbon::parse($data->date)->translatedFormat("l, d F Y"); }}'
                                    readonly />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Lokasi/Tempat @if($data->type == "M") Rapat @else Acara
                                    @endif</label>
                                <input type="text" class="form-control" name="lokasi" value="{{ $data->location }}"
                                    readonly />
                            </div>
                            @if($data->expired != null)
                            <div class="mb-3">
                                <label class="form-label">Tenggat waktu Absensi</label>
                                <input type="text" class="form-control" name="tenggat"
                                    value='{{ \Carbon\Carbon::parse($data->expired)->translatedFormat("l, d F Y H:i"); }}'
                                    readonly />
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Paraf Digital <strong
                                        class="text-danger">*</strong></label><br>
                                @if($check !=null)
                                <img src="{!! $check->signature_img !!}" style="width: 300px; height:150px;"
                                    alt="Paraf" />
                                @else
                                <div class="row text-center text-md-start">
                                    <div class="clearfix">
                                        <div id="sig" style="width: 300px; height:150px;"
                                            class="@error('paraf') border-danger @enderror"></div>
                                        <br />
                                        <div class="text-md-start w-100">
                                            <button id="clear" class="btn btn-outline-secondary btn-sm">Bersihkan
                                                kanvas</button>
                                            @error('paraf')
                                            <small class="text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </small>
                                            @enderror
                                            <input id="signature64" name="paraf" type="hidden" style="display: none">
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <hr>
                        <div class="mb-3 text-center">
                            <button class="btn btn-success w-100" type="submit" name="attendance" @if($check !=null)
                                disabled @endif><i class="bx bx-log-in-circle me-2"></i>Absen Sekarang</button>
                        </div>
                        <div class="mb-3 text-center">
                            <button class="btn btn-dark w-100" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                <i class="bx bx-log-out-circle me-2"></i>Keluar
                            </button>
                        </div>
                    </form>
                    @endif
            </div>
        </div>
    </div>
</div>
</div>
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
@endsection

@section('script')
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="{{asset('assets/js/jquery.signature.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/js/geo.js')}}"></script>
<script type="text/javascript" src="https://www.openlayers.org/api/OpenLayers.js"></script>
<script type="text/javascript">
    var sig = $('#sig').signature({
        syncField: '#signature64',
        syncFormat: 'PNG'
    });
    $('#clear').click(function (e) {
        e.preventDefault();
        sig.signature('clear');
        $("#signature64").val('');
    });

</script>
<script type="text/javascript">
    function initialize() {
        if (geo_position_js.init()) {
            document.getElementById('current').innerHTML = "Receiving...";
            geo_position_js.getCurrentPosition(show_position, function () {
                document.getElementById('map_canvas').innerHTML =
                    "Sistem tidak bisa mendeteksi lokasi anda,<br>harap untuk menyalakan GPS<br>dan mengizinkan sistem ini."
            }, {
                enableHighAccuracy: true
            });
        } else {
            document.getElementById('map_canvas').innerHTML = "Functionality not available";
        }
    }

    function show_position(p) {
        document.getElementById('current').innerHTML = "latitude=" + p.coords.latitude + " longitude=" + p.coords
            .longitude;
        $("#latitude").val(p.coords.latitude);
        $("#longitude").val(p.coords.longitude);
        document.getElementById("googleMap").href = "https://www.google.com/maps?q=loc:" + p.coords.latitude + "," + p
            .coords.longitude;
        console.log(p.coords.latitude + " " + p.coords.longitude);

        map = new OpenLayers.Map("map_canvas");
        map.addLayer(new OpenLayers.Layer.OSM());

        @if($check == null)
        var lonLat = new OpenLayers.LonLat(p.coords.longitude, p.coords.latitude)
            .transform(
                new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
                map.getProjectionObject() // to Spherical Mercator Projection
            );
        @else
        var lonLat = new OpenLayers.LonLat("{{ $check->longitude }}", "{{ $check->latitude }}")
            .transform(
                new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
                map.getProjectionObject() // to Spherical Mercator Projection
            );
        document.getElementById("googleMap").style.display = 'none';
        @endif



        var zoom = 16;

        var markers = new OpenLayers.Layer.Markers("Markers");
        map.addLayer(markers);

        markers.addMarker(new OpenLayers.Marker(lonLat));

        map.setCenter(lonLat, zoom);
    }

</script>
@endsection
