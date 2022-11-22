<html>

<head>
    <title>MoM - {{ $data->title }} | {{ \Carbon\Carbon::parse($data->date)->translatedFormat("l, d F Y"); }}
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

    </style>
    <style>
        tbody td {
            vertical-align: top;
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
            <td valign="top" colspan="2" style="text-align:center;" width="50%">
                <p><b>Risalah @if($data->type =='E') Acara @elseif($data->type =='M') Rapat @endif<b></p>
            </td>
            <td style="text-align:center; vertical-align:middle;" rowspan='6' width="25%">
                <p style="margin-bottom: 5px;">FM/JGU/L.008</p>
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
            <td valign="top" width="30%">
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
        <thead>
            <tr style="vertical-align:middle">
                <th style="text-align:center;vertical-align:middle" width="5%">
                    <p>NO</p>
                </th>
                <th style="text-align:center;" width="70%">
                    <p>URAIAN RAPAT</p>
                </th>
                <th style="text-align:center;" width="15%">
                    <p>PIC</p>
                </th>
                <th style="text-align:center;" width="10%">
                    <p>TARGET</p>
                </th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach($lists->sortBy('id') as $d)
            <tr style="vertical-align:middle">
                <td style="text-align:center;">
                    <p>{{$i++}}</p>
                </td>
                <td>
                    <p>{!! $d->detail !!}</p>
                </td>
                <td>
                    <p>
                        @php $pic = []; $pics = "";
                        foreach($d->pics as $key => $p){
                        array_push($pic, $p->name_with_title);
                        }
                        $pics = implode(",<br>",$pic);
                        if(strlen($pics) > 150){
                        $pics = substr($pics,0,150).".. dll.";
                        }
                        @endphp
                        {!! $pics; !!}
                    </p>
                </td>
                <td style="text-align:center;">
                    <p>{!! $d->target !!}</p>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <table width="100%" style="text-align: center;">
        <tr>
            <td width="40%">
                Pimpinan @if($data->type =='E') Acara @elseif($data->type =='M') Rapat @endif
                <br><br><br><br><b>({{$data->host}})</b>
            </td>
            <td width="20%">

            </td>
            <td width="40%">
                Notulen
                <br><br><br><br><b>({{$data->notulen->name_with_title}})</b>
            </td>
        </tr>
    </table>
    <div class="page-break"></div>
    <br>
    <center>
        <h5><u>DOKUMENTASI @if($data->type =='E') ACARA @elseif($data->type =='M') RAPAT @endif</u></h5>
    </center>
    <br>
    @foreach($images as $key => $s)
    <center>
        <img src="{{ public_path($s->doc_path) }}" style="max-width: 15.2cm; max-height:15cm"><br>
        <small style="font-size: 8pt">Dokumentasi {{$key+1}}</small>
    </center><br>
    @endforeach
    @if(count($docs) != 0)
    <div class="page-break"></div>
    <br>
    <center>
        <h5><u>LAMPIRAN DOKUMEN @if($data->type =='E') ACARA @elseif($data->type =='M') RAPAT @endif</u></h5>
    </center>
    <br>
    Tautan URL:
    <ul>
        @foreach($docs as $key => $p)
        <li><a href="{{ asset($p->doc_path) }}" target="_blank">{{$p->doc_path}}</a>
        </li>
        @endforeach
    </ul>
    @endif
    <script type="text/php">
        if (isset($pdf)) {
            $x = 40;
            $y = 550;
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $font = null;
            $size = 8;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
    </script>
</body>

</html>
