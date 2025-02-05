<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Thermal with QZ Tray</title>
    {{-- <script src="{{ asset('qz-tray/qz-tray.js') }}"></script> <!-- Sesuaikan path ke file qz-tray.js --> --}}
    <script src="https://cdn.jsdelivr.net/npm/qz-tray/qz-tray.js"></script>

</head>

<body>
    <h1>Print Thermal Demo</h1>
    <button onclick="printThermal()">Print</button>

    <script>
        qz.api.setPromiseType(resolver => new Promise(resolver));

        // Setting hashing SHA-256
        qz.api.setSha256Type(data =>
            crypto.subtle.digest("SHA-256", new TextEncoder().encode(data))
                .then(hash => Array.from(new Uint8Array(hash))
                    .map(byte => byte.toString(16).padStart(2, "0")).join(""))
        );

        // Hubungkan ke QZ Tray
        qz.websocket.connect()
            .then(() => console.log("QZ Tray connected"))
            .catch(err => console.error("QZ Tray connection failed", err));

        async function printThermal() {
            try {
                // Cek apakah printer tersedia
                const printer = await qz.printers.find("POS-58");
                console.log("Printer ditemukan:", printer);

                // Ambil data cetak dari Laravel (pastikan endpoint sesuai)
                const response = await fetch("{{ route('getPrintData', ['202501221002098713']) }}");
                const printData = await response.json();

                // Format tabel dengan padding
                let content = "=============================" + "\n";
                content += "| Item       | Qty | Price  |" + "\n";
                content += "=============================" + "\n";

                printData.items.forEach(item => {
                    let nama = item.cart_nama.padEnd(10, ' ');
                    let qty = String(item.cart_qty).padStart(3, ' ');
                    let harga = `Rp ${item.cart_harga_jual}`.padStart(10, ' ');
                    content += `| ${nama} | ${qty} | ${harga} |\n`;
                });

                content += "=============================" + "\n";
                console.log(content);
                // Konfigurasi printer
                const config = qz.configs.create(printData.printer, {
                    fontSize: printData['font-size'],
                });

                // Data yang akan dikirim ke printer
                const data = [{
                    type: 'raw',
                    format: 'plain',
                    data: content
                }];

                // Kirim perintah cetak
                await qz.print(config, data);
                console.log("Print job successful");
            } catch (error) {
                console.error("Error printing", error);
                alert("Error during print: " + error.message);
            }
        }

        // Pastikan QZ Tray terputus saat halaman ditutup
        window.onbeforeunload = function() {
            if (qz.websocket.isActive()) {
                qz.websocket.disconnect();
            }
        };
    </script>
</body>

</html>
