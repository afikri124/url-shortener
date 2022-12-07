<html>

<head>
    <title>Rekap Jam Kerja</title>
</head>

<body>
    <table style="font-size: 11pt;">
        <thead>
            <tr>
                <td style="text-align: right;vertical-align: top;" colspan="2">
                    <img src="{{ public_path('assets/img/logo_small.png') }}" style="height: 20px;" alt="Logo">
                </td>
                <td colspan="3" style="text-align: center;font-weight: bold;vertical-align: middle;">
                    REKAP JAM KERJA KARYAWAN
                    @if($periode != null)
                        <br>Periode : {{$periode}}
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="5"></td>
            </tr>
            <tr>
                <th style="text-align: center;font-weight: bold;width:50px">No</th>
                <th style="text-align: center;font-weight: bold;width:250px">Nama</th>
                <th style="text-align: center;font-weight: bold;width:130px">NIK</th>
                <th style="text-align: center;font-weight: bold;width:100px">Total Hari</th>
                <th style="text-align: center;font-weight: bold;width:100px">Total Jam</th>
            </tr>
        </thead>
        <tbody>
            @php $nokey = 0; @endphp
            @foreach($data as $d)
            <tr>
                <td style="vertical-align: top;text-align:center">{{ ++$nokey }}</td>
                <td style="vertical-align: top;">
                    @if($d->name2 != null)
                    {{ $d->name2 }}
                    @else
                    {{ $d->name }} *
                    @endif
                </td>
                <td style="vertical-align: top;text-align:center">{{ str_replace("\x00","",$d->username) }}</td>
                <td style="vertical-align: top;text-align:center">{{ $d->hari }}</td>
                <td style="vertical-align: top;text-align:center">{{ $d->total }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <i>* Nama Di Mesin</i>
                </td>
                <td colspan="3" style="text-align: center;">
                    <b>Depok, {{ \Carbon\Carbon::now()->translatedFormat("d F Y") }}</b>
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
