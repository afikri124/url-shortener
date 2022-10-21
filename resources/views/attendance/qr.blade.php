<html>

<head>
    <title>Event Attendances {{ $data->id."-".$tok }} | {{ Date::now()->format('j F Y') }}
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
        <b style="font-size:30px">{{$data->title}}</b>
        <br>
        <b style="font-size:70px">SCAN HERE</b>
        <br>
        <b style="font-size:20px;">BEFORE YOU JOIN THIS @if($data->type =='E') EVENT @elseif($data->type =='M') MEETING @endif !</b>
        <br>
        <a href="{{$link}}" target="_blank" ><img src="{!! $qr !!}" style="height: 300px;margin:40px 0 20px 0;"></a>
        <br>
        <h4>[ <code style="color:red">{{$link}}</code> ]</h4>
        <br>
        <b style="font-size:20px ">{{$data->location}}</b>
        <br>
        <i style="font-size:15px ">{{date('l, d F Y', strtotime($data->date))}}</i>
      <hr>
        <br>
        <b style="font-size:15px ">HOW TO SCAN THE QR CODE</b>
        <br>
        <br>
    </center>
    <table width="100%"  style="font-size:13px;">
        <tr>
            <td width="30%" style="text-align: center;">
            <h1>1.</h1>
            <p>Using barcode scanner</p>
            </td>
            <td width="40%" style="text-align: center;">
            <h1>2.</h1>
            <p>Login with your account</p>
            </td>
            <td width="30%" style="text-align:center;">
            <h1>3.</h1>
            <p>Fill up the details</p>
            </td>
        </tr>
    </table>
    <hr>
</body>

</html>