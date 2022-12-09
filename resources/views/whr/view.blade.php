@extends('layouts.master')
@section('title', $user->name )
@section('breadcrumb-items')
<span class="text-muted fw-light">Absensi / Rekap Jam Kerja /</span>
@endsection

@section('content')
<!-- Header -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="row p-2 px-4 ">
                <table width="100%">
                    <tr>
                        <td width="110px">
                            @if($user->user != null)
                            <img src="{{ $user->user->image() }}" class="rounded" width="100px">
                            @else
                            <img src="{{asset('assets/img/avatars/user.png')}}" class="rounded" width="100px">
                            @endif
                        </td>
                        <td>
                            <div>
                                <h5>{{ ($user->user != null? $user->user->name_with_title : $user->name) }}</h5>
                                <i class='bx bx-id-card'></i> {{ $user->username }}
                            </div>
                        </td>
                        <td style="text-align: right;">
                            <a href="{{$link}}" target="_blank"><img src="https://s.jgu.ac.id/qrcode?data={{$link}}" style="height: 85px;"></a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<!--/ Header -->
<!-- User Profile Content -->
<div class="row">
    <div class="col-12">
        <!-- Projects table -->
        <div class="card mb-4">
            <div class="card-datatable table-responsive">
                <div class="card-header">
                    <div class="row">
                        <div class="col-11">
                            <span>Laporan Absensi Jam Kerja</span><br><span>Periode : <b> {{ $periode }}</b></span>
                        </div>
                        <div class="col-1 text-end">
                            <a onclick="window.print()" class="btn btn-light m-0 p-0 text-info text-end d-none d-md-block"
                                title="Cetak halaman ini"><i class="bx bx-printer"></i></a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <table class="table table-hover table-striped table-sm" width="100%">
                            <thead>
                                <tr>
                                    <th width="70px">Hari</th>
                                    <th>Tanggal</th>
                                    <th class="d-none d-lg-table-cell text-center" width="100px">User Id</th>
                                    <th width="100px" class="text-center">Masuk</th>
                                    <th width="100px" class="text-center">Keluar</th>
                                    <th width="100px" class="text-center">Telat</th>
                                    <th width="100px" class="text-center">P.Cepat</th>
                                    <th width="100px" class="text-center">Lembur</th>
                                    <th width="100px" data-priority="3" class="text-end">JMLH.Jam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $totalJam = 0;
                                    $totalMasuk = 0;
                                    $totalKeluar = 0;
                                    $totalTelat = 0;
                                    $totalCepat = 0;
                                    $totalLembur = 0; 
                                    $totalAbsen = 0;
                                @endphp
                                @foreach($data as $key => $d)
                                <tr>
                                    <td
                                    @if((\Carbon\Carbon::parse($d->tanggal))->dayOfWeek == \Carbon\Carbon::SUNDAY)
                                    class="text-danger"
                                    @elseif((\Carbon\Carbon::parse($d->tanggal))->dayOfWeek == \Carbon\Carbon::SATURDAY)
                                    class="text-warning"
                                    @endif
                                    >
                                        {{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat("l")}}
                                    </td>
                                    <td class="d-lg-none text-end">{{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat("d/m/Y")}}</td>
                                    <td class="d-none d-lg-table-cell">{{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat("d F Y")}}</td>
                                    @if($d->username != null)
                                    @php $totalAbsen++; @endphp
                                    <td class="d-none d-lg-table-cell text-center"><code>{{$d->username}}</code></td>
                                    <td class="text-center">
                                        @if($d->masuk != $d->keluar)
                                            @php $totalMasuk++; @endphp
                                            {{ \Carbon\Carbon::parse($d->masuk)->translatedFormat("H:i")}}
                                        @elseif($d->masuk <= \Carbon\Carbon::parse($d->tanggal." 16:00"))
                                            {{ \Carbon\Carbon::parse($d->masuk)->translatedFormat("H:i")}}
                                            @php $totalMasuk++; @endphp
                                        @else
                                        <i class='bx bx-block'></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($d->masuk != $d->keluar)
                                            @php $totalKeluar++; @endphp
                                            {{ \Carbon\Carbon::parse($d->keluar)->translatedFormat("H:i")}}
                                        @elseif($d->keluar > \Carbon\Carbon::parse($d->tanggal." 16:00"))
                                            {{ \Carbon\Carbon::parse($d->keluar)->translatedFormat("H:i")}}
                                            @php $totalKeluar++; @endphp
                                        @else
                                        <i class='bx bx-block'></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php 
                                            if(\Carbon\Carbon::parse($d->masuk) > \Carbon\Carbon::parse($d->tanggal." 08:00:00")){
                                                $telat = (\Carbon\Carbon::parse($d->masuk))->diff(\Carbon\Carbon::parse($d->tanggal." 08:00:00"))->format('%H:%I:%S');
                                                $temp = explode(":", $telat);
                                                $totalTelat += (int) $temp[0] * 3600;
                                                $totalTelat += (int) $temp[1] * 60;
                                                $totalTelat += (int) $temp[2];
                                                echo \Carbon\Carbon::parse($telat)->translatedFormat("H:i");
                                            }
                                        @endphp
                                    </td>
                                    <td class="text-center">
                                        @php 
                                        if((\Carbon\Carbon::parse($d->tanggal))->dayOfWeek == \Carbon\Carbon::SATURDAY){
                                            if((\Carbon\Carbon::parse($d->keluar) < \Carbon\Carbon::parse($d->tanggal." 14:00:00")) && $d->total_jam != '00:00:00'){
                                                $cepat = (\Carbon\Carbon::parse($d->keluar))->diff(\Carbon\Carbon::parse($d->tanggal." 14:00:00"))->format('%H:%I:%S');
                                                $temp = explode(":", $cepat);
                                                $totalCepat += (int) $temp[0] * 3600;
                                                $totalCepat += (int) $temp[1] * 60;
                                                $totalCepat += (int) $temp[2];
                                                echo \Carbon\Carbon::parse($cepat)->translatedFormat("H:i");
                                            }
                                        } else {
                                            if((\Carbon\Carbon::parse($d->keluar) < \Carbon\Carbon::parse($d->tanggal." 16:00:00")) && $d->total_jam != '00:00:00'){
                                                $cepat =  (\Carbon\Carbon::parse($d->keluar))->diff(\Carbon\Carbon::parse($d->tanggal." 16:00:00"))->format('%H:%I:%S');
                                                $temp = explode(":", $cepat);
                                                $totalCepat += (int) $temp[0] * 3600;
                                                $totalCepat += (int) $temp[1] * 60;
                                                $totalCepat += (int) $temp[2];
                                                echo \Carbon\Carbon::parse($cepat)->translatedFormat("H:i");
                                            }
                                        }
                                        @endphp
                                    </td>
                                    <td class="text-center">
                                        @php 
                                            if(\Carbon\Carbon::parse($d->total_jam) > new \Carbon\Carbon("10:00:00")){
                                                $lembur = (\Carbon\Carbon::parse($d->total_jam))->diff(new \Carbon\Carbon("10:00:00"))->format('%h:%I:%S');
                                                $temp = explode(":", $lembur);
                                                $totalLembur += (int) $temp[0] * 3600;
                                                $totalLembur += (int) $temp[1] * 60;
                                                $totalLembur += (int) $temp[2];
                                                echo \Carbon\Carbon::parse($lembur)->translatedFormat("H:i");
                                            }
                                        @endphp
                                    </td>
                                    <td class="text-end">
                                        @php 
                                        $jam = \Carbon\Carbon::parse($d->total_jam)->translatedFormat("H:i:s");
                                        $temp = explode(":", $jam);
                                        $totalJam += (int) $temp[0] * 3600;
                                        $totalJam += (int) $temp[1] * 60;
                                        $totalJam += (int) $temp[2];
                                        @endphp
                                        {{$jam}}
                                    </td>
                                    @else
                                    <td class="d-none d-lg-table-cell text-center"></td>
                                    <td class="text-center"><i class='bx bx-block'></i></td>
                                    <td class="text-center"><i class='bx bx-block'></i></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                                <tr>
                                    <th>Total</th>
                                    <th class="text-center" title="Total Hari">
                                        @if ($totalAbsen < 20)
                                            <b class='text-danger' title='Total < 20'>{{$totalAbsen}}<b>
                                        @else
                                            <b class='text-success' title='Total >= 20'>{{$totalAbsen}}<b>
                                        @endif
                                        / {{count($data)}} Hari
                                    </th>
                                    <th class="text-center d-none d-lg-table-cell" width="100px">
                                        @if ($totalAbsen < 20)
                                            <b class='text-danger' title='Total < 20'>{{$totalAbsen}}<b>
                                        @else
                                            <b title='Total >= 20'>{{$totalAbsen}}<b>
                                        @endif
                                    </th>
                                    <th class="text-center" title="Total Abensi Masuk">
                                        @if ($totalMasuk < 20)
                                            <b class='text-danger' title='Total < 20'>{{$totalMasuk}}<b>
                                        @else
                                            <b title='Total >= 20'>{{$totalMasuk}}<b>
                                        @endif
                                        x
                                    </th>
                                    <th class="text-center" title="Total Abensi Keluar">
                                        @if ($totalKeluar < 20)
                                            <b class='text-danger' title='Total < 20'>{{$totalKeluar}}<b>
                                        @else
                                            <b title='Total >= 20'>{{$totalKeluar}}<b>
                                        @endif
                                        x
                                    </th>
                                    <th class="text-center text-danger">
                                        @php
                                            $print = sprintf('%02d:%02d',
                                                ($totalTelat / 3600),
                                                ($totalTelat / 60 % 60),
                                                $totalTelat % 60);
                                            if ($print != "00:00") {
                                                echo "<b class='text-danger' title='Total Telat'>".$print."<b>";
                                            } else {
                                                echo $print;
                                            }
                                        @endphp
                                    </th>
                                    <th class="text-center">
                                        @php
                                            $print = sprintf('%02d:%02d',
                                                ($totalCepat / 3600),
                                                ($totalCepat / 60 % 60),
                                                $totalCepat % 60);
                                            if ($print != "00:00") {
                                                echo "<b class='text-danger' title='Total Pulang Cepat'>".$print."<b>";
                                            } else {
                                                echo $print;
                                            }
                                        @endphp
                                    </th>
                                    <th class="text-center">
                                        @php
                                            $print = sprintf('%02d:%02d',
                                                ($totalLembur / 3600),
                                                ($totalLembur / 60 % 60),
                                                $totalLembur % 60);
                                            if ($print != "00:00") {
                                                echo "<b class='text-success' title='Total Jam Lembur'>".$print."<b>";
                                            } else {
                                                echo $print;
                                            }
                                        @endphp
                                    </th>
                                    <th class="text-end">
                                        @php 
                                            $print = sprintf('%02d:%02d:%02d',
                                                ($totalJam / 3600),
                                                ($totalJam / 60 % 60),
                                                $totalJam % 60);
                                            $x = explode(":", $print);
                                            if (intval($x[0]) < 160) {
                                                echo "<b class='text-danger' title='Total Jam Kerja < 160'>".$print."<b>";
                                            } else {
                                                echo "<b class='text-success' title='Total Jam Kerja >= 160'>".$print."<b>";
                                            }
                                        @endphp
                                    </th>
                                </tr>
                        </table>
                        <br>
                        <hr class="mt-3">
                        <small class="text-info">Catatan : 
                            <ul>
                                <li>Senin - Jumat : 08:00 - 16:00 </li>
                                <li>Sabtu : 08:00 - 14:00 </li>
                                <li>Minggu : <i>(opsional)</i> </li>
                            </ul>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Projects table -->
    </div>
</div>
<!--/ User Profile Content -->
@endsection
