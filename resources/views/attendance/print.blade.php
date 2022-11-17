<html>

<head>
    <title>Absensi {{ $data->id."-".$tok }} | {{ \Carbon\Carbon::parse($data->date)->translatedFormat("l, d F Y"); }}
    </title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        html {
            margin: 1.5cm;
        }

        .page-break {
            page-break-after: always;
        }

        p {
            margin: 0 5px;
        }

        a {
            margin: 0;
            padding: 0;
        }

    </style>
    <style>
        tbody td {
            vertical-align: middle;
            word-wrap: break-word;
        }

        td:nth-child(1) {
            max-width: 120px;
        }

    </style>
</head>

<body style="font-size: 10pt;">
    <table width="100%" border="1px solid">
        <tr>
            <td style="text-align:center; vertical-align:middle;" rowspan='6' width="25%">
                <img src="{{ public_path('assets/img/jgu.png') }}" style="height: 60px;" alt="">
            </td>
            <td valign="top" colspan="2" style="text-align:center;" width="55%">
                <p><b>Presensi Kehadiran @if($data->type =='E') Acara @elseif($data->type =='M') Rapat @endif<b></p>
            </td>
            <td style="text-align:center; vertical-align:middle;" rowspan='6' width="20%">
                <p style="margin-bottom: 5px;">FM/JGU/L.007</p>
                <a href="{{ $link }}"><img src="{{ $qr }}" style="height: 85px;"></a>
            </td>
        </tr>
        <tr>
            <td valign="top" colspan="2" style="text-align:center;">
                <p><b>{{$data->title}}</b></p>
            </td>
        </tr>
        <tr>
            <td valign="top" width="20%">
                <p>Hari/Tgl.</p>
            </td>
            <td valign="top" width="35%">
                <p>{{ \Carbon\Carbon::parse($data->date)->translatedFormat("l, d F Y"); }}</p>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <p>Tempat</p>
            </td>
            <td valign="top">
                <p> {{$data->location}}</p>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <p>Pimpinan @if($data->type =='E') Acara @elseif($data->type =='M') Rapat @endif</p>
            </td>
            <td valign="top">
                <p> {{$data->host}}</p>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <p>Peserta @if($data->type =='E') Acara @elseif($data->type =='M') Rapat @endif</p>
            </td>
            <td valign="top">
                <p> {{$data->participant}}</p>
            </td>
        </tr>
    </table>
    <table width="100%" border="1px solid">
        <tr style="vertical-align:middle">
            <td style="text-align:center;vertical-align:middle" width="5%">
                <p>No</p>
            </td>
            <td style="text-align:center;" width="40%">
                <p>Nama</p>
            </td>
            <td style="text-align:center;" width="35%">
                <p>Jabatan</p>
            </td>
            <td style="text-align:center;" width="20%">
                <p>Paraf</p>
            </td>
        </tr>
        @php $i = 1; $datamerah = false; @endphp
        @foreach($al->sortBy('user.name_with_title') as $d)
        <tr style="vertical-align:middle">
            <td style="text-align:center;">
                <p>{{$i++}}</p>
            </td>
            <td>
                @if($d->longitude == null)
                @php $datamerah = true; @endphp
                <p style="color:red;" title="Lokasi">{{($d->user != null ? $d->user->name_with_title : $d->username)}}
                </p>
                @else
                <p>
                    <a style="color:black;" target='_blank'
                        href='https://www.google.com/maps?q={{ $d->latitude}},{{$d->longitude}}'>
                        {{($d->user != null ? $d->user->name_with_title : $d->username)}}
                    </a>
                </p>
                @endif

            </td>
            <td>
                <p>{{($d->user != null ? $d->user->job : "-")}}</p>
            </td>
            <td style="text-align:center;">
                <p><img src="{!! $d->signature_img !!}" style="height:40px; margin:0px;" /></p>
            </td>
        </tr>
        @endforeach
        @if(count($al2) != 0)
            <tr style="background-color: #999; color:#fff">
                <td colspan="4" style="text-align: center;"><small>Tidak masuk menggunakan Akun karyawan JGU</small></td>
            </tr>
        @endif
        @foreach($al2->sortBy('user.name_with_title') as $d)
        <tr style="vertical-align:middle">
            <td style="text-align:center;">
                <p>{{$i++}}</p>
            </td>
            <td>
                @if($d->longitude == null)
                @php $datamerah = true; @endphp
                <p style="color:red;">{{($d->user != null ? $d->user->name_with_title : $d->username)}}
                </p>
                @else
                <p>
                    <a style="color:black;" target='_blank'
                        href='https://www.google.com/maps?q={{ $d->latitude}},{{$d->longitude}}'>
                        {{($d->user != null ? $d->user->name_with_title : $d->username)}}
                    </a>
                </p>
                @endif

            </td>
            <td>
                <p><i>{{($d->user != null ? $d->user->job : "-")}}</i></p>
            </td>
            <td style="text-align:center;">
                <p><img src="{!! $d->signature_img !!}" style="height:40px; margin:0px;" /></p>
            </td>
        </tr>
        @endforeach
    </table>
    @if($datamerah)
        <small><p style="color:red;font-size:6pt">* Warna Merah : Titik lokasi Absensi tidak ditemukan.</p></small>
    @endif
    <br>
    <table width="100%">
        <tr>
            <td width="60%">
            </td>
            <td width="40%">Pimpinan @if($data->type =='E') Acara @elseif($data->type =='M') Rapat @endif
                <br><br><br><br><b>{{$data->host}}</b></td>
        </tr>
    </table>
</body>

</html>
