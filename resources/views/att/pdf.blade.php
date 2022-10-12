<html>

<head>
    <title>Event Attendances | {{ Date::now()->format('j F Y') }}
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
    <table width="100%">
        <tr>
            <td width="50%" valign="top">
               
            </td>
            <td width="50%" style="text-align: right;">
                <img src="{{ public_path('assets/img/jgu.png') }}" style="height: 60px;" alt="">
            </td>
        </tr>
    </table>
    <br>
    <center>
        <b style="font-size:50px">{{$data->title}}</b>
        <br>
        <b style="font-size:100px">SCAN HERE</b>
        <br>
        <b style="font-size:30px;">BEFORE YOU JOIN THIS EVENT!</b>
        <br>
        <a href="{{$link}}" target="_blank" ><img src="{!! $qr !!}" style="height: 350px;margin:75px 0;"></a>
        <br>
        <b style="font-size:50px ">{{$data->location}}</b>
      <hr>
        <br>
        <b style="font-size:20px ">HOW TO SCAN THE QR CODE</b>
    </center>
    <table width="100%">
        <tr>
            <td width="30%" valign="top">
            <b style="font-size:20px;">1. Using barcode scanner</b>
            </td>
            <td width="40%" style="text-align: center;">
            <b style="font-size:20px;">2. Login with your account</b>
            </td>
            <td width="30%" style="text-align:right;">
            <b style="font-size:20px;">3. Fill up the details</b>
            </td>
        </tr>
    </table>
</body>

</html>