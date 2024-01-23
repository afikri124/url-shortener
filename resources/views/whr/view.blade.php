@extends('layouts.master')
@section('title', $user->name )
@section('breadcrumb-items')
<span class="text-muted fw-light">Absensi / Rekap Jam Kerja /</span>
@endsection

@section('style')
<style>
    @media print
    {    
        .no-print, .no-print *
        {
            display: none !important;
        }
        #template-customizer {
            display: none !important;
        }
    }
    .table-sm>:not(caption)>*>* {
        padding: 0.1rem;
    }
</style>
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
                            @if($photo != null)
                            <img src="{{ $photo }}"  class="rounded"
                            style="object-fit: cover;" width="100px" height="100px">
                            @else
                            <img src="{{asset('assets/img/avatars/user.png')}}" class="rounded" width="100px">
                            @endif
                        </td>
                        <td>
                            <div>
                                <h5>{{ ($user->user != null? $user->user->name_with_title : $user->name) }}</h5>
                                {{ $user->username }}
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
                        <table class="table table-hover table-sm" width="100%" style="padding:0 2px;">
                            <thead>
                                <tr style="border-bottom: 2px double">
                                    <th width="60px">Hari</th>
                                    <th>Tanggal</th>
                                    <th class="d-none d-lg-table-cell text-center" width="100px">User Id</th>
                                    <th width="90px" class="text-center">Masuk</th>
                                    <th width="90px" class="text-center">Keluar</th>
                                    <th width="90px" class="text-center">Telat</th>
                                    <th width="90px" class="text-center">P.Cepat</th>
                                    <th width="90px" class="text-center">Lembur</th>
                                    <th width="90px" class="text-center">Kurang</th>
                                    <th width="100px" data-priority="3" class="text-end">Total JAM/Hari</th>
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
                                    $totalKurang = 0; 
                                    $totalAbsen = 0;
                                    $totalJamPerminggu = 0;
                                @endphp
                                @foreach($data as $key => $d)
                                <tr
                                @if((\Carbon\Carbon::parse($d->tanggal))->dayOfWeek == \Carbon\Carbon::SUNDAY)
                                    class="text-danger"
                                    @elseif((\Carbon\Carbon::parse($d->tanggal))->dayOfWeek == \Carbon\Carbon::SATURDAY)
                                    class="text-primary"
                                    @endif
                                >
                                    <td>
                                        {{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat("l")}}
                                    </td>
                                    <td class="d-lg-none text-end">{{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat("d/m/Y")}}</td>
                                    <td class="d-none d-lg-table-cell">{{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat("d F Y")}}</td>
                                    @if($d->username != null)
                                    @php $totalAbsen++; @endphp
                                    <td class="d-none d-lg-table-cell text-center"><code>{{$d->username}}</code></td>
                                    <td class="text-center">
                                        {{-- MASUK --}}
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
                                        {{-- KELUAR --}}
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
                                        {{-- TELAT --}}
                                        @php 
                                            if(\Carbon\Carbon::parse($d->masuk) > \Carbon\Carbon::parse($d->tanggal." 08:00:59")){
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
                                        {{-- PULANG CEPAT --}}
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
                                        } elseif ((\Carbon\Carbon::parse($d->tanggal))->dayOfWeek == \Carbon\Carbon::SUNDAY){
                                            echo "";
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
                                    <td class="text-center text-success">
                                        {{-- LEMBUR --}}
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
                                    <td class="text-center text-danger">
                                        {{-- KURANG --}}
                                        @php 
                                        if((\Carbon\Carbon::parse($d->tanggal))->dayOfWeek == \Carbon\Carbon::SATURDAY){
                                            if(\Carbon\Carbon::parse($d->total_jam) < new \Carbon\Carbon("06:00:00")){
                                                $kurang = (\Carbon\Carbon::parse($d->total_jam))->diff(new \Carbon\Carbon("06:00:00"))->format('%h:%I:%S');
                                                $temp = explode(":", $kurang);
                                                $totalKurang += (int) $temp[0] * 3600;
                                                $totalKurang += (int) $temp[1] * 60;
                                                $totalKurang += (int) $temp[2];
                                                echo \Carbon\Carbon::parse($kurang)->translatedFormat("H:i");
                                            }
                                        } elseif ((\Carbon\Carbon::parse($d->tanggal))->dayOfWeek == \Carbon\Carbon::SUNDAY){
                                            echo "";
                                        } else {
                                            if(\Carbon\Carbon::parse($d->total_jam) < new \Carbon\Carbon("08:00:00")){
                                                $kurang = (\Carbon\Carbon::parse($d->total_jam))->diff(new \Carbon\Carbon("08:00:00"))->format('%h:%I:%S');
                                                $temp = explode(":", $kurang);
                                                $totalKurang += (int) $temp[0] * 3600;
                                                $totalKurang += (int) $temp[1] * 60;
                                                $totalKurang += (int) $temp[2];
                                                echo \Carbon\Carbon::parse($kurang)->translatedFormat("H:i");
                                            }
                                        }
                                        @endphp
                                    </td>
                                    <td class="text-end"> 
                                        {{-- TOTAL --}}
                                        @php
                                        $jam = \Carbon\Carbon::parse($d->total_jam)->translatedFormat("H:i:s");
                                        $temp = explode(":", $jam);
                                        $totalJam += (int) $temp[0] * 3600;
                                        $totalJam += (int) $temp[1] * 60;
                                        $totalJam += (int) $temp[2];

                                        $totalJamPerminggu += (int) $temp[0] * 3600;
                                        $totalJamPerminggu += (int) $temp[1] * 60;
                                        $totalJamPerminggu += (int) $temp[2];
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
                                    <td></td>
                                    @endif
                                </tr>
                                @if((\Carbon\Carbon::parse($d->tanggal))->dayOfWeek == \Carbon\Carbon::SUNDAY)
                                <tr style="background-color:rgba(67,89,113,.04); border-bottom: 1px ridge yellow">
                                <td class="d-none d-lg-table-cell text-center"></td>
                                    <td colspan="7" class="text-light "><i>Total jam/minggu</i></td>
                                    <td></td>
                                    <td class="text-end">
                                        @php 
                                            $print = sprintf('%02d:%02d:%02d',
                                                ($totalJamPerminggu / 3600),
                                                ($totalJamPerminggu / 60 % 60),
                                                $totalJamPerminggu % 60);
                                            $x = explode(":", $print);
                                            if (intval($x[0]) < 40) {
                                                echo "<i class='text-danger' title='Jam kerja/minggu < 40'>".$print."<i>";
                                            } else {
                                                echo "<i class='text-success' title='Jam kerja/minggu >= 40'>".$print."<i>";
                                            }
                                            $totalJamPerminggu = 0;
                                        @endphp
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                                <tr style="font-size: 13pt;">
                                    <td>Total</td>
                                    <td class="text-center" title="Total Hari">
                                        @if ($totalAbsen < 20)
                                            <b class='text-danger' title='Total < 20'>{{$totalAbsen}}<b>
                                        @else
                                            <b class='text-success' title='Total >= 20'>{{$totalAbsen}}<b>
                                        @endif
                                        / {{count($data)}} Hari
                                    </td>
                                    <td class="text-center d-none d-lg-table-cell" width="100px">
                                        @if ($totalAbsen < 20)
                                            <b class='text-danger' title='Total < 20'>{{$totalAbsen}}<b>
                                        @else
                                            <b title='Total >= 20'>{{$totalAbsen}}<b>
                                        @endif
                                    </td>
                                    <td class="text-center" title="Total Abensi Masuk">
                                        @if ($totalMasuk < 20)
                                            <b class='text-danger' title='Total < 20'>{{$totalMasuk}}<b>
                                        @else
                                            <b title='Total >= 20'>{{$totalMasuk}}<b>
                                        @endif
                                        x
                                    </td>
                                    <td class="text-center" title="Total Abensi Keluar">
                                        @if ($totalKeluar < 20)
                                            <b class='text-danger' title='Total < 20'>{{$totalKeluar}}<b>
                                        @else
                                            <b title='Total >= 20'>{{$totalKeluar}}<b>
                                        @endif
                                        x
                                    </td>
                                    <td class="text-center text-danger">
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
                                    </td>
                                    <td class="text-center">
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
                                    </td>
                                    <td class="text-center">
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
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $print = sprintf('%02d:%02d',
                                                ($totalKurang / 3600),
                                                ($totalKurang / 60 % 60),
                                                $totalKurang % 60);
                                            if ($print > "00:00") {
                                                echo "<b class='text-danger' title='Total Jam Kurang'>".$print."<b>";
                                            } else {
                                                echo $print;
                                            }
                                        @endphp
                                    </td>
                                    <td class="text-end">
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
                                    </td>
                                </tr>
                        </table>
                        <br>
                        
                    </div>
                </div>
            </div>
        </div>
        <!--/ Projects table -->
    </div>
</div>
<!--/ User Profile Content -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="row p-2 px-4 ">
                <div class="page-break"></div>
                        <small class="text-muted"><u>Aturan Jam Kerja</u>
                            <table>
                                <tr>
                                    <td>-</td>
                                    <td>Senin-Jumat</td>
                                    <td>: 08:00 - 16:00 WIB</td>
                                </tr>
                                <tr>
                                    <td>-</td>
                                    <td>Sabtu</td>
                                    <td>: 08:00 - 14:00 WIB</td>
                                </tr>
                                <tr>
                                    <td>-</td>
                                    <td>Minggu</td>
                                    <td>: <i>Kondisional</i></td>
                                </tr>
                                <tr>
                                    <td>-</td>
                                    <td>Lembur</td>
                                    <td>: <i>Setelah 10 jam</i></td>
                                </tr>
                            </table>
                        </small>
            </div>
        </div>
    </div>
</div>
@endsection
@if(request()->route()->getPrefix() != "/WHR")
@section('script')
<script type="text/javascript">
window.onload = function() { window.print(); }
</script>
@endsection
@endif
