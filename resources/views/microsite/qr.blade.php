<html>

<head>
    <title>MICROSITE {{ $tok }} | {{ \Carbon\Carbon::now()->translatedFormat("l, d F Y"); }}
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

        .header,
        .footer {
            width: 100%;
            text-align: center;
            position: fixed;
        }

        .header {
            right: -20px;
            top: -20px;
            bottom: 300px;
        }

        .footer {
            bottom: -60px;
        }

        .pagenum:before {
            content: counter(page);
        }

        body {
            margin-top: 45px;
            margin-bottom: 25px;
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

<body style="font-size: 11pt;">
    <div class="header">
        <table width="100%">
            <tr>
                <td width="50%" valign="top">

                </td>
                <td width="50%" style="text-align: right;">
                    <img src="{{ public_path('assets/img/jgu.png') }}" style="height: 60px;" alt="">
                </td>
            </tr>
        </table>
    </div>
    <div class="footer">
        {{-- Page <span class="pagenum"></span> --}}
        <img src="{{ public_path('assets/img/footer_jgu_2025.png') }}" style="width:127%" alt="">
    </div>
    <br>
    <center>
        {{-- <b style="font-size:50px">SCAN DISINI</b>
        <br> --}}
        <b style="font-size:30px">{{$data->title}}</b><br>
        <i style="font-size:16px">Silahkan Scan Kode QR berikut ini</i>
        <br>
        <a href="{{$link}}" target="_blank"><img src="{!! $qr !!}" style="height: 300px;margin:20px 0 20px 0;"></a>
        <br>
        <h4>[ <code style="color:red">{{$link}}</code> ]</h4>
        <br>

    </center>

</body>

</html>
