<html>

<head>
    <title>QR Absensi {{ $data->id."-".$tok }} | {{ \Carbon\Carbon::parse($data->date)->translatedFormat("l, d F Y"); }}
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
            top: -20px;
            bottom: 300px;
        }

        .footer {
            bottom: -63px;
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
        <img src="{{ public_path('assets/img/footer_jgu_2025.png') }}" style="width:117%" alt="">
    </div>
    <div class="body-page">
        <center>
            <b style="font-size:50px">SCAN DISINI</b>
            <br>
            <b style="font-size:20px;">UNTUK MELAKUKAN ABSENSI @if($data->type =='E') ACARA @elseif($data->type =='M')
                RAPAT
                @endif</b>
            <br>
            <b style="font-size:30px">{{$data->title}}</b>
            <br>
            <a href="{{$link}}" target="_blank"><img src="{!! $qr !!}" style="height: 250px;margin:40px 0 20px 0;"></a>
            <br>
            <h4>[ <code style="color:red">{{$link}}</code> ]</h4>
            <br>
            <b style="font-size:20px ">{{$data->location}}</b>
            <br>
            <i style="font-size:15px ">{{ \Carbon\Carbon::parse($data->date)->translatedFormat("l, d F Y"); }}</i>
            <hr>
            <br>
            <b style="font-size:15px ">LANGKAH MELAKUKAN ABSENSI</b>
            <br>
            <br>
        </center>
        <table width="100%" style="font-size:13px;">
            <tr>
                <td width="30%" style="text-align: center;">
                    <h1>1.</h1>
                    <p>Menggunakan pemindai QR-Code</p>
                </td>
                <td width="40%" style="text-align: center;">
                    <h1>2.</h1>
                    <p>Masuk dengan akun Anda</p>
                </td>
                <td width="30%" style="text-align:center;">
                    <h1>3.</h1>
                    <p>Isikan detail data yang sesuai</p>
                </td>
            </tr>
        </table>
        <hr>
        <blockquote>
            <small style="font-size: 8pt;color:#999">
                <center><i>
                        Civitas Akademica JGU (Mahasiswa/Staf/Dosen) silakan masuk melalui Single Sign-On (SSO) Google dengan menggunakan email @jgu.ac.id, sedangkan
                        untuk tamu (eksternal) dapat masuk menggunakan akun Gmail.<br>
                        Jika terdapat kendala login atau belum memiliki akun SSO silakan menghubungi tim HR / ITIC.</i>
                </center>
            </small>
        </blockquote>
    </div>
    
</body>

</html>
