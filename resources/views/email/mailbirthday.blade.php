@extends('email.template')
@section('title', $data['subject'])
@section('content')

@php

@endphp

<table align="left" border="0" cellpadding="0" cellspacing="0" style="text-align: left;" width="100%">
    <tbody>
        <tr>
            <td style="font-size: 10pt;">
            </td>
        </tr>
        <tr>
            <td>
                <div style="font-family: Arial, sans-serif; padding:20px;">
                    <div style=" margin:0; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1); padding:30px;">
                        <h2 style="color:#28a745; text-align:center;">🎂 Selamat Ulang Tahun! 🎂</h2>
                        <h1  style="text-align:center;"><strong>{{ $data['name'] }}</strong></h1>
                        @if (!$data['is_mhs'])
                        <p>Seluruh keluarga besar <strong>Jakarta Global University</strong> mengucapkan selamat ulang
                            tahun untuk Anda.
                            Semoga selalu diberikan kesehatan, kebahagiaan, dan kesuksesan dalam setiap langkah.</p>
                        <p style="margin-top:20px;">Terima kasih atas dedikasi dan kontribusi Anda dalam membangun
                            institusi ini. 😊</p>
                        <p style="text-align:right; margin-top:40px;">Salam hangat,<br><strong>Administrator
                                JGU</strong></p>
                        @else
                        <p>Seluruh civitas akademika <strong>Jakarta Global University</strong> mengucapkan
                            selamat ulang tahun untukmu.
                            Semoga panjang umur, sehat selalu, dan semakin berprestasi dalam perjalanan studi
                            serta kehidupanmu.</p>
                        <p style="margin-top:20px;">Teruslah bersemangat dalam meraih mimpi dan cita-citamu. 😊</p>
                        <p style="text-align:right; margin-top:40px;">Salam hangat,<br><strong>Administrator
                                JGU</strong></p>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </tbody>
</table>

@endsection
