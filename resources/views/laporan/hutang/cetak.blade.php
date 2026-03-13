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
        <h3>LAPORAN HUTANG </h3>
        <h3> Di {{ strtoupper($cabang_nama_view) }}</h3>
        <h4>
            {{ \Carbon\Carbon::parse($res_date_start)->translatedFormat('d F Y') }} -
            {{ \Carbon\Carbon::parse($res_date_end)->translatedFormat('d F Y') }}
        </h4>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 10px">No</th>
                <th style="text-align: center">ID Keranjang</th>
                <th style="text-align: center">Date Time</th>
                <th style="text-align: center">Pelanggan</th>
                <th style="text-align: center">Total Pembelian</th>
                <th style="text-align: center">Uang Muka</th>
                <th style="text-align: center">Kekurangan</th>
                <th style="text-align: center">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $ttlPembelian = 0;
                $ttlMuka = 0;
                $ttlKekurangan = 0;
            @endphp
            @foreach ($transaksi as $key => $hutang)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ $hutang->cart_id }}</td>
                <td style="text-align: center;">
                    {{ \Carbon\Carbon::parse($hutang->trans_date)->translatedFormat('d F Y H:i') }}
                </td>
                <td>{{ $hutang->trans_pelanggan }}</td>
                <td style="text-align: right; font-weight: bold; color: green;">{{ 'Rp. ' . number_format($hutang->trans_total, 0, ',', '.') }}</td>
                <td style="text-align: right; font-weight: bold; color: blue;">{{ 'Rp. ' . number_format($hutang->cart->cart_draft->draft_uang_muka, 0, ',', '.') }}</td>
                <td style="text-align: right; font-weight: bold; color: red;">{{ 'Rp. ' . number_format($hutang->cart->cart_draft->draft_uang_sisa, 0, ',', '.') }}</td>
                <td>{{ $hutang->cart->cart_draft->draft_note}}</td>
                @php
                    $ttlPembelian += $hutang->trans_total;
                    $ttlMuka += $hutang->cart->cart_draft->draft_uang_muka;
                    $ttlKekurangan += $hutang->cart->cart_draft->draft_uang_sisa;
                @endphp
            </tr>
            @endforeach
            <tr class="text-bold">
                <td colspan="4" style="text-align: right; font-weight: bold;">Jumlah</td>
                <td style="text-align: right; font-weight: bold; color: green;">{{ 'Rp. ' . number_format($ttlPembelian, 0, ',', '.') }}</td>
                <td style="text-align: right; font-weight: bold; color: blue;">{{ 'Rp. ' . number_format($ttlMuka, 0, ',', '.') }}</td>
                <td style="text-align: right; font-weight: bold; color: red;">{{ 'Rp. ' . number_format($ttlKekurangan, 0, ',', '.') }}</td>
                <td style="text-align: center;">
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
