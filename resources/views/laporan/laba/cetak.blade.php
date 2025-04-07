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
        <h3>LAPORAN LABA RUGI </h3>
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
                <th style="text-align: center">Harga Beli</th>
                <th style="text-align: center">Harga Jual</th>
                <th style="text-align: center">Laba</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $ttlBeli = 0;
                $ttlJual = 0;
                $ttlQrt = 0;
                $grandTtl = 0;
                $grandTtlCartBeli = 0;
                $grandTtlCartJual = 0;
                $grandTtlLaba = 0;
            @endphp
            @foreach ($data as $key => $laba)
                @php
                    $ttlBeli += $laba->cart_harga_beli * $laba->cart_qty;
                    $ttlJual += $laba->cart_harga_jual * $laba->cart_qty;
                    $ttlQrt += $laba->cart_qty;
                    $grandTtl += $laba->cart_subtotal;
                    $jlhCartData = count($laba->cart_data);
                    //
                    $ttlCartBeli = 0;
                    $ttlCartJual = 0;
                @endphp
                @foreach ($laba->cart_data as $item)
                    @php
                        $ttlCartBeli += $item->cart_harga_beli * $item->cart_qty;
                        $ttlCartJual += $item->cart_harga_jual * $item->cart_qty;
                        // laba
                        $ttlLaba = $ttlCartJual - $ttlCartBeli;
                    @endphp
                @endforeach
                <tr>
                    <td style="text-align: center">{{ $no++ }}</td>
                    <td style="text-align: center">{{ $laba->cart_id }}</td>
                    <td style="text-align: center">
                        {{ \Carbon\Carbon::parse($laba->trans_date)->translatedFormat('d F Y H:i') }}
                    </td>
                    <td>{{ $laba->trans_pelanggan }}</td>
                    <td style="text-align: right; font-weight: bold; color: red;">
                        {{ 'Rp. ' . number_format($ttlCartBeli, 0, ',', '.') }}</td>
                    <td style="text-align: right; font-weight: bold; color: blue;">
                        {{ 'Rp. ' . number_format($ttlCartJual, 0, ',', '.') }}</td>
                    <td style="text-align: right; font-weight: bold; color: green;">
                        {{ 'Rp. ' . number_format($ttlLaba, 0, ',', '.') }}</td>
                    @php
                        $grandTtlCartBeli += $ttlCartBeli;
                        $grandTtlCartJual += $ttlCartJual;
                        $grandTtlLaba += $ttlLaba;
                    @endphp
                </tr>
            @endforeach
            <tr>
                <td class="text-right" colspan="4"></td>
                <td style="text-align: right; font-weight: bold; color: red;">
                    {{ 'Rp. ' . number_format($grandTtlCartBeli, 0, ',', '.') }}</td>
                <td style="text-align: right; font-weight: bold; color: blue;">
                    {{ 'Rp. ' . number_format($grandTtlCartJual, 0, ',', '.') }}</td>
                <td style="text-align: right; font-weight: bold; color: green;">
                    {{ 'Rp. ' . number_format($grandTtlLaba, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
