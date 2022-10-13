<html>

<head>
    <title>Meeting Attendances {{ $data->id."-".$tok }} | {{ Date::now()->format('j F Y') }}
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
            vertical-align: middle;
            word-wrap: break-word;
        }

        td:nth-child(1) {
            max-width: 120px;
        }

    </style>
</head>

<body style="font-size: 11pt;">
    <table width="100%" border="1px solid">
        <tr>
            <td style="text-align:center; vertical-align:middle;" rowspan='6' width="25%">
                <img src="{{ public_path('assets/img/jgu.png') }}" style="height: 60px;" alt="">
            </td>
            <td valign="top" colspan="2" style="text-align:center;" width="55%">
                <p>{{$data->title}}</p>
            </td>
            <td style="text-align:center; vertical-align:middle;" rowspan='6' width="20%">
                <p>FM/JGU/L.007</p>
            </td>
        </tr>
        <tr>
            <td valign="top" colspan="2" style="text-align:center;">
                <p>{{$data->sub_title}}</p>
            </td>
        </tr>
        <tr>
            <td valign="top" width="20%">
                <p>Date</p>
            </td>
            <td valign="top" width="35%">
                <p>{{ date('l, d F Y', strtotime($data->date))}}</p>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <p>Location</p>
            </td>
            <td valign="top">
                <p> {{$data->location}}</p>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <p>Host</p>
            </td>
            <td valign="top">
                <p> {{$data->host}}</p>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <p>Participant</p>
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
                <p>Name</p>
            </td>
            <td style="text-align:center;" width="35%">
                <p>Job</p>
            </td>
            <td style="text-align:center;" width="20%">
                <p>Date Sign</p>
            </td>
        </tr>
        @foreach($al as $key => $d)
        <tr style="vertical-align:middle">
            <td style="text-align:center;">
                <p>{{$key+1}}</p>
            </td>
            <td>
                <p>{{$d->user->name}}</p>
            </td>
            <td>
                <p>{{$d->user->job}}</p>
            </td>
            <td style="text-align:center;">
                <p>{{$d->user->created_at}}</p>
            </td>
        </tr>
        @endforeach
    </table>
    <br>
    <br>
    <table width="100%">
        <tr>
            <td width="60%">
            </td>
            <td width="40%">Meeting Host <br><br><br><br><b>{{$data->host}}</b></td>
        </tr>
    </table>
</body>

</html>
