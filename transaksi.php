<?php
// Variabel dengan berbeda tipe data
$nama_pembeli = "";     // ini string
$jumlah_beli = 0;       // ini integer
$harga_satuan = 0.0;    // ini float
$member = false;        // ini boolean

$barang = "";
$status_pembeli = "";
$total_belanja = 0;
$diskon = 0;
$total_bayar = 0;
$pesan_error = "";

// Ada Array yang berisi 6 barang dan harganya
$daftar_barang = [
    "Buku Tulis" => 5000.0,
    "Pulpen" => 3000.0,
    "Penghapus" => 2000.0,
    "Pensil" => 2500.0,
    "Penggaris" => 4000.0,
    "Spidol" => 8000.0
];

// Function buatan sendiri menghitung total
function hitungTotal($harga, $jumlah)
{
    return $harga * $jumlah;
}

// Function untuk menampilkan format rupiah
function formatRupiah($nominal)
{
    return "Rp" . number_format($nominal, 0, ",", ".");
}

// Mengecek form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pembeli = trim($_POST["nama_pembeli"]);
    $barang = $_POST["barang"];
    $jumlah_beli = (int) $_POST["jumlah_beli"];
    $member = $_POST["member"] == "1";

    // memeriksa data yg dimasukan
    if (
        $nama_pembeli == "" ||
        !isset($daftar_barang[$barang]) ||
        $jumlah_beli < 1 ||
        $jumlah_beli > 999
    ) {
        $pesan_error = "Lengkapi data dan isi jumlah beli sesuai kebutuhan antara 1-999";
    } else {
        $harga_satuan = $daftar_barang[$barang];
        $total_belanja = hitungTotal($harga_satuan, $jumlah_beli);

        // Percabangan untuk diskon member
        if ($member) {
            $diskon = $total_belanja * 0.10;
            $status_pembeli = "Member";
        } else {
            $diskon = 0;
            $status_pembeli = "Nonmember";
        }

        $total_bayar = $total_belanja - $diskon;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Toko Alat Tulis</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 30px 15px;
            color: #334155;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background-color: white;
            padding: 25px;
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #234f7d;
            font-size: 26px;
        }

        h2 {
            color: #234f7d;
            font-size: 20px;
        }

        ol {
            background-color: #eef5fa;
            padding: 15px 15px 15px 40px;
            border-radius: 8px;
        }

        li {
            padding: 4px 0;
        }

        .form-group {
            margin-bottom: 15 px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            font: inherit;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #234f7d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font: inherit;
        }

        button:hover {
            background-color: #163652;
        }

        .hasil {
            margin-top: 25px;
            padding: 20px;
            background-color: #eef5fa;
            border-radius: 8px;
        }

        .total {
            border-top: 1px solid #cbd5e1;
            padding-top: 12px;
            color: #234f7d;
            font-weight: bold;
        }

        .error {
            color: #b91c1c;
        }
    </style>
</head>
<body>
    <div class="container">
         <h1>Toko Alat Tulis Sahuurr</h1>

         <h2>Daftar Barang</h2>

         <ol>
            <?php
            // Perulangan menampilkan barang
            foreach ($daftar_barang as $nama_barang => $harga) {
                echo "<li>" . $nama_barang . " - " . formatRupiah($harga) . "</li>";
            } 
            ?>
         </ol>

         <h2>Input Transaksi</h2>

         <form method="POST">
            <div class="form-group">
                <label for="nama_pembeli">Nama Pembeli</label>
                <input type="text" id="nama_pembeli" name="nama_pembeli" required>
            </div>

            <div class="form-group">
                <label for="barang">Pilih Barang</label>
                <select id="barang" name="barang" required>
                    <option value="">-- Pilih Barang --</option>
                    <option value="Buku Tulis">Buku Tulis</option>
                    <option value="Pulpen">Pulpen</option>
                    <option value="Penghapus">Penghapus</option>
                    <option value="Pensil">Pensil</option>
                    <option value="Penggaris">Penggaris</option>
                    <option value="Spidol">Spidol</option>
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah_beli">Jumlah Beli</label>
                <input type="number" id="jumlah_beli" name="jumlah_beli" min="1" max="999" step="1" required>
            </div>

            <div class="form-group">
                <label for="member">Status Pembeli</label>
                <select id="member" name="member">
                    <option value="0">Nonmember</option>
                    <option value="1">Member (Diskon 10%)</option>
                </select>
            </div>

            <button type="submit">Hitung Transaksi</button>
         </form>

         <?php if ($_SERVER["REQUEST_METHOD"] == "POST") { ?>

            <?php if ($pesan_error != "") { ?>
                <p class="error"><?= $pesan_error ?></p>

             <?php } else { ?>

             <div class="hasil">
                <h2>Rincian Transaksi</h2>

                <p>
                    Nama pembeli:
                    <?=  htmlspecialchars($nama_pembeli, ENT_QUOTES, "UTF-8") ?>
                </p>

                <p>Status: <?= $status_pembeli ?></p>
                <p>Barang: <?= htmlspecialchars($barang, ENT_QUOTES, "UTF-8") ?></p>
                <p>Harga satuan: <?= formatRupiah($harga_satuan) ?></p>
                <p>Jumlah beli: <?= $jumlah_beli ?>buah</p>
                <p>Total belanja: <?= formatRupiah($total_belanja) ?></p>
                <p>Diskon: <?= formatRupiah($diskon) ?></p>

                <p class="total">
                    Total bayar: <?= formatRupiah($total_bayar) ?>
                </p>
             </div>

         <?php } ?>

    <?php } ?>

</div> 
</body>
</html>