@extends('email.template')
@section('title', $data['subject'])
@section('content')

@php
    $pagi = "Pagi!";
    $var = date('H');
    $no_urut = 1;
    if ($var >= 4 && $var < 11) {
        $pagi = 'Pagi!';
    } else if ($var >= 11 && $var < 16) {
        $pagi = 'Siang!';
    } else if ($var >= 16 && $var < 18) {
        $pagi = 'Sore!';
    } else {
        $pagi = 'Malam!';
    }
@endphp
<table align="left" border="0" cellpadding="0" cellspacing="0" style="text-align: left;" width="100%">
    <tbody>
        <tr>
            <td style="font-size: 10pt;">
                <p><i>Selamat {{$pagi}}</i></p>
                <p>Yang Terhormat Bapak/Ibu <b>{{ $data['name'] }}</b>,</p><br>
                <p style="text-align: justify;">{!! $data['messages'] !!}</p>
            </td>
        </tr>
    </tbody>
</table>
@if(isset($data['item1']) || isset($data['item2']))
<table class="order-detail" border="1" cellpadding="0" cellspacing="0" align="left"
    style="width: 100%; font-size:10pt;">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Total <br><i>tidak masuk</i></th>
        </tr>
    </thead>
    <tbody>
        @if(isset($data['item1']))
        <tr>
            <tr>
                <td colspan="3">Karyawan Tetap JGU <i>(Penuh waktu)</i></td>
            </tr>
        </tr>
            @foreach($data['item1'] as $d)
            <tr class="pad-left-right-space">
                <td align="center" valign="top" width="5px">
                    <p>{{$no_urut++}}</p>
                </td>
                <td align="left">
                    <a href="{{ url('WHR/view/') }}/{{$d[2]}}?range={{$data['period']}}" target="_blank">{!! $d[0] !!}</a>
                </td>
                <td align="center">
                    <strong>{!! $d[1] !!} hari</strong>
                </td>
            </tr>
            @endforeach
        @endif
        @if(isset($data['item2']))
        <tr>
            <tr>
                <td colspan="3">Karyawan JGU <i>(Eksternal)</i></td>
            </tr>
        </tr>
            @foreach($data['item2'] as $d)
            <tr class="pad-left-right-space">
            <td align="center" valign="top" width="5px">
                    <p>{{$no_urut++}}</p>
                </td>
                <td align="left">
                    <a href="{{ url('WHR/view/') }}/{{$d[2]}}?range={{$data['period']}}" target="_blank">{!! $d[0] !!}</a>
                </td>
                <td align="center">
                    <strong>{!! $d[1] !!} hari</strong>
                </td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>
@endif
<table align="left" border="0" cellpadding="0" cellspacing="0" style="text-align: center; margin-bottom:50px;font-size: 10pt;"
    width="100%">
    <tbody>
        <tr>
            <td>
            {!! $data['catatan'] !!}
            </td>
        </tr>
        <tr>
            <td><br>
                <hr>
                <p>Jika terdapat kendala <strong>jangan membalas email ini</strong>, silahkan menghubungi Tim ITIC JGU.</p>
                <p>Terima Kasih.</p>
            </td>
        </tr>
    </tbody>
</table>
@endsection