<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Barcode</title>

    <style>
        .text-center { text-align: center; }
        table { border-collapse: collapse; width: 100%; }
        td { padding: 10px; border: 1px solid #333; vertical-align: top; }
    </style>
</head>
<body>
    <table>
        <tr>
            @foreach ($dataproduk as $item)
                <td class="text-center">
                    <p>{{ $item->name }}</p>
                    <img src="data:image/png;base64,{{ base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->format('png')->generate($item->code)) }}">
                    <br>
                    {{ $item->code }}
                </td>
                @if ($loop->iteration % 3 == 0)
                    </tr><tr>
                @endif
            @endforeach
        </tr>
    </table>
</body>
</html>