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
@if(isset($data['kegiatan']))
<table class="order-detail" border="0" cellpadding="0" cellspacing="0" align="left"
    style="width: 100%; font-size:10pt;">
    <tbody>
        @foreach($data['kegiatan'] as $d)
        <tr class="pad-left-right-space">
            <td align="left" valign="top" width="35px">
                <p><strong>{{$no_urut++}}</strong></p>
            </td>
            <td align="left">
                <p>{{ $d }}</p>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
<table align="left" border="0" cellpadding="0" cellspacing="0" style="text-align: left; margin-bottom:50px;"
    width="100%">
    <tbody>
        <tr>
            <td style="font-size: 10pt;">
                <div>{!! $data['catatan'] !!}</div><br>
                <p style="text-align: justify;">Informasi selengkapnya silahkan login ke dalam <a
                        href="{{url('/home')}}">sistem.</a>
                    <br>Jika terdapat kendala <strong>jangan membalas email ini</strong>, silahkan menghubungi Tim ITIC JGU.</p>
                <br>
                <p>Terima Kasih.</p>
            </td>
        </tr>
    </tbody>
</table>
@endsection