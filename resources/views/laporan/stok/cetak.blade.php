<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>
    <div style="text-align: center">
        <h3>Laporan Barang Paling Laku </h3>
        <h3>Di {{ strtoupper($cabang_nama_view) }}</h3>
    </div>
    <table>
        <thead>
            @if ($cabang_id == 'gudang')
                <tr>
                    <th style="width: 10px" style="text-align: center">No</th>
                    <th style="text-align: center">Nama Barang</th>
                    <th style="text-align: center">Jumlah</th>
                </tr>
            @else
                <tr>
                    <th style="width: 10px" style="text-align: center">No</th>
                    <th style="text-align: center">Cabang</th>
                    <th style="text-align: center">Nama Barang</th>
                    <th style="text-align: center">Jumlah</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @php
                $no = 1;
                $grandTotal = 0;
            @endphp
            @if ($cabang_id == 'gudang')
                @foreach ($data as $key => $terbanyak)
                    <tr>
                        <td style="text-align: center">{{ $no++ }}</td>
                        <td>{{ $terbanyak->barang_nama }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $terbanyak->penjualan }}</td>
                    </tr>
                    @php
                        $grandTotal += $terbanyak->penjualan;
                    @endphp
                @endforeach
            @else
                @foreach ($data as $key => $terbanyak)
                    <tr>
                        <td style="text-align: center">{{ $no++ }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $terbanyak->cabang_nama }}</td>
                        <td>{{ $terbanyak->barang_nama }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $terbanyak->cart_qty }}</td>
                    </tr>
                    @php
                        $grandTotal += $terbanyak->cart_qty;
                    @endphp
                @endforeach
            @endif
            <tr>
                @if ($cabang_id == 'gudang')
                    <td colspan="2" style="text-align: right; font-weight: bold;">Total</td>
                @else
                    <td colspan="3" style="text-align: right; font-weight: bold;">Total</td>
                @endif
                <td style="text-align: center; font-weight: bold;">{{ $grandTotal }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
