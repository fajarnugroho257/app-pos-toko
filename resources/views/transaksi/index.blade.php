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
        // Inisialisasi QZ Tray
        qz.api.setPromiseType(function(resolver) {
            return new Promise(resolver); // Pastikan menggunakan 'new' dengan Promise
        });

        // Setting untuk hashing (SHA-256)
        qz.api.setSha256Type(function(data) {
            return crypto.subtle.digest("SHA-256", new TextEncoder().encode(data))
                .then(function(hash) {
                    return Array.from(new Uint8Array(hash)).map(function(byte) {
                        return byte.toString(16).padStart(2, "0");
                    }).join("");
                });
        });

        // Hubungkan ke QZ Tray
        qz.websocket.connect()
            .then(() => console.log("QZ Tray connected"))
            .catch(err => console.error("QZ Tray connection failed", err));

        async function printThermal() {
            try {
                qz.printers.find("POS-58").then(printer => {
                    console.log("Printer ditemukan:", printer);
                }).catch(err => {
                    console.error("Printer tidak ditemukan:", err);
                });
                // Ambil data cetak dari Laravel (pastikan endpoint sesuai)
                const response = await fetch("{{ route('dataPrint') }}");
                const printData = await response.json();

                // Konfigurasi printer
                const config = qz.configs.create(printData.options.printer, {
                    fontSize: printData.options['font-size'], // Opsional: Sesuaikan opsi lainnya
                });

                // Data yang akan dicetak
                const data = [{
                    type: 'raw',
                    format: 'plain',
                    data: printData.content
                }];

                // Kirim perintah cetak
                await qz.print(config, data);
                console.log("Print job successful");
                // alert("Print job sent successfully!");
            } catch (error) {
                console.error("Error printing", error);
                alert("Error during print: " + error.message);
            }
        }

        // Opsional: Memastikan QZ Tray terputus saat halaman ditutup
        window.onbeforeunload = function() {
            if (qz.websocket.isActive()) {
                qz.websocket.disconnect();
            }
        };
    </script>
</body>

</html>
