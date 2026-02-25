<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table>
        {{-- Mengambil Header dari excelHeader.blade.php --}}
        @include('asetTetap.excelHeader')

        <tbody>
            @foreach ($asets as $aset)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td style="text-align: center;">{{ $aset->code }}</td>
                <td style="text-align: center;">{{ $aset->nup }}</td>
                <td>{{ $aset->name }}</td>
                <td>{{ $aset->name_fix }}</td>
                <td>Rp{{ number_format($aset->nilai, 0, ',', '.') }}</td>
                <td style="text-align: center;">{{ $aset->years }}</td>
                
                {{-- Lokasi: Diambil langsung berdasarkan ID tanpa @foreach ganda --}}
                <td>
                    @php $loc = $locations->firstWhere('id', $aset->store_location); @endphp
                    {{ $loc ? ($loc->office . ' - Lt ' . $loc->floor . ' - R ' . $loc->room) : '-' }}
                </td>

                <td style="text-align: center;">{{ $aset->condition }}</td>
                
                {{-- Penanggung Jawab --}}
                <td>
                    @php $emp = $employees->firstWhere('id', $aset->supervisor); @endphp
                    {{ $emp ? $emp->name : '-' }}
                </td>

                <td>{{ $aset->documentation ?? '-' }}</td>
                <td>{{ $aset->description }}</td>
                <td style="text-align: center;">{{ $aset->bulan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>