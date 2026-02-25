<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Barcode</title>

    <style>
        .text-center {
            text-align: center;
        }

        p{
            font-size: 12px;
        }
    </style>
</head>

<body>
    <table width="100%">
        <tr >
            @foreach ($qrcode as $qr)
                <td>
                    <table style="border-collapse: collapse; height: 90px;"  >
                        <tr>
                            <td class="text-center" style="padding:10px; border: 1px solid #333;">
                                <img src="{{ public_path('assets/img/pupr.png') }}" width="40" height="40" alt="">
                            </td>
                            <td colspan="2" class="text-center" style="border: 1px solid #333;">
                                <p><b>KEMENTERIAN PEKERJAAN UMUM DAN PERUMAHAN RAKYAT</b></p>
                                <p>{{ $dataproduk[$no]->code . '.' . $dataproduk[$no]->years }}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:5px; border: 1px solid #333;" colspan="2">
                                <p>Balai Sains Bangunan</p>
                                <p>{{ $dataproduk[$no]->name }}</p>
                                <p>Merk : {{ $dataproduk[$no]->name_fix }}</p>
                            </td>
                            <td class="text-center" style=" padding:5px; border: 1px solid #333;">
                                <img src="data:image/png;base64, {!! $qr !!}" style="width: 120px;">
                            </td>
                        </tr>
                    </table>
                </td>
                    @if ($loop->iteration % 2 === 0)
                        </tr>
                        <tr>
                    @endif
                    {{ $no++ }}
            @endforeach
        </tr>
    </table>
</body>

</html>
