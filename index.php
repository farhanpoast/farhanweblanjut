<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guestbook Sederhana</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Buku Tamu Sederhana</h1>

        <div class="form-section">
            <h2>Tinggalkan Pesan</h2>
            <form action="index.php" method="post">
                <div class="form-group">
                    <label for="nama">Nama:</label>
                    <input type="text" id="nama" name="nama" required>
                </div>
                <div class="form-group">
                    <label for="pesan">Pesan:</label>
                    <textarea id="pesan" name="pesan" rows="5" required></textarea>
                </div>
                <button type="submit">Kirim Pesan</button>
            </form>
        </div>

        <?php
        // Lokasi file data guestbook
        $dataFile = 'data.txt';

        // --- Proses Penambahan Pesan Baru ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama']);
            $pesan = trim($_POST['pesan']);
            $tanggal = date('Y-m-d H:i:s'); // Format Tanggal dan Waktu

            // Validasi sederhana
            if (!empty($nama) && !empty($pesan)) {
                // Bersihkan input untuk mencegah masalah baris baru atau karakter khusus
                $nama = str_replace(array("\n", "\r", "|"), '', $nama);
                $pesan = str_replace(array("\n", "\r"), ' ', $pesan); // Ganti newlines di pesan dengan spasi
                $pesan = str_replace("|", "-", $pesan); // Ganti | di pesan dengan - agar tidak merusak format

                // Format data untuk disimpan: Tanggal | Nama | Pesan
                $dataBaru = $tanggal . " | " . $nama . " | " . $pesan . "\n";

                // Tambahkan data baru ke file data.txt
                // FILE_APPEND: Menambahkan ke akhir file
                // LOCK_EX: Mengunci file saat menulis untuk mencegah korupsi data saat diakses bersamaan
                if (file_put_contents($dataFile, $dataBaru, FILE_APPEND | LOCK_EX) !== false) {
                    echo "<p class='success-message'>Terima kasih, pesan Anda telah ditambahkan!</p>";
                } else {
                    echo "<p class='error-message'>Maaf, terjadi kesalahan saat menyimpan pesan.</p>";
                }
            } else {
                echo "<p class='error-message'>Nama dan pesan tidak boleh kosong.</p>";
            }
        }

        // --- Tampilkan Pesan-Pesan yang Sudah Ada ---
        echo "<div class='entries-section'>";
        echo "<h2>Pesan-Pesan Pengunjung</h2>";

        if (file_exists($dataFile) && filesize($dataFile) > 0) {
            // Membaca semua baris dari file
            $entries = file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            // Membalik urutan agar pesan terbaru muncul di atas
            $entries = array_reverse($entries);

            if (empty($entries)) {
                echo "<p>Belum ada pesan. Jadilah yang pertama!</p>";
            } else {
                foreach ($entries as $entry) {
                    // Memecah setiap baris menjadi bagian-bagian: Tanggal, Nama, Pesan
                    $parts = explode(' | ', $entry, 3); // Batasi menjadi 3 bagian

                    $tanggal_display = isset($parts[0]) ? htmlspecialchars($parts[0]) : 'Tidak Diketahui';
                    $nama_display = isset($parts[1]) ? htmlspecialchars($parts[1]) : 'Anonim';
                    $pesan_display = isset($parts[2]) ? htmlspecialchars($parts[2]) : 'Tidak ada pesan.';

                    echo "<div class='guestbook-entry'>";
                    echo "<p class='entry-meta'><strong>Nama:</strong> " . $nama_display . " | <strong>Tanggal:</strong> " . $tanggal_display . "</p>";
                    echo "<p class='entry-message'>" . nl2br($pesan_display) . "</p>"; // nl2br untuk menampilkan baris baru di pesan
                    echo "</div>";
                }
            }
        } else {
            echo "<p>Belum ada pesan. Jadilah yang pertama!</p>";
        }
        echo "</div>";
        ?>
    </div>
</body>
</html>
