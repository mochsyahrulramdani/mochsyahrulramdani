<?php

declare(strict_types=1);

session_start();

date_default_timezone_set('Asia/Jakarta');

// ==========================================
// 1. MEMANGGIL CLASS
// ==========================================

require_once __DIR__ . '/ProdukCafe.php';
require_once __DIR__ . '/MenuCafe.php';

// ==========================================
// 2. MENYIAPKAN SESSION APLIKASI
// ==========================================

$_SESSION['ah_master_cafe'] ??= [];

$app =& $_SESSION['ah_master_cafe'];

$app['token'] ??= bin2hex(random_bytes(32));

// ==========================================
// 3. FUNGSI BANTU
// ==========================================

// Mengamankan teks sebelum ditampilkan.
function h($nilai): string
{
    return htmlspecialchars(
        (string) $nilai,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Mengubah angka menjadi format rupiah.
function rupiah(int $nominal): string
{
    return 'Rp' . number_format(
        $nominal,
        0,
        ',',
        '.'
    );
}

// ==========================================
// 4. DATA AWAL MENU
// ==========================================

$dataAwalMenu = [
    'M001' => [
        'nama' => 'Nasi Goreng Spesial',
        'harga' => 25000,
        'stok' => 20,
        'deskripsi' => 'Nasi goreng dengan bumbu spesial.',
        'kategori' => 'Makanan',
        'ukuran' => 'Regular',
        'pedas' => 'Sedang',
        'rekomendasi' => true
    ],

    'M002' => [
        'nama' => 'Mie Goreng',
        'harga' => 18000,
        'stok' => 15,
        'deskripsi' => 'Mie goreng dengan bumbu pilihan.',
        'kategori' => 'Makanan',
        'ukuran' => 'Regular',
        'pedas' => 'Sedang',
        'rekomendasi' => false
    ],

    'M003' => [
        'nama' => 'Ayam Geprek',
        'harga' => 22000,
        'stok' => 10,
        'deskripsi' => 'Ayam geprek dengan sambal.',
        'kategori' => 'Makanan',
        'ukuran' => 'Regular',
        'pedas' => 'Pedas',
        'rekomendasi' => true
    ],

    'M004' => [
        'nama' => 'Es Teh Manis',
        'harga' => 8000,
        'stok' => 30,
        'deskripsi' => 'Minuman teh manis dingin.',
        'kategori' => 'Minuman',
        'ukuran' => 'Regular',
        'pedas' => 'Tidak ada',
        'rekomendasi' => false
    ],

    'M005' => [
        'nama' => 'Kopi Susu',
        'harga' => 15000,
        'stok' => 20,
        'deskripsi' => 'Kopi dengan campuran susu.',
        'kategori' => 'Minuman',
        'ukuran' => 'Regular',
        'pedas' => 'Tidak ada',
        'rekomendasi' => true
    ],

    'M006' => [
        'nama' => 'Jus Alpukat',
        'harga' => 18000,
        'stok' => 12,
        'deskripsi' => 'Jus alpukat segar.',
        'kategori' => 'Minuman',
        'ukuran' => 'Regular',
        'pedas' => 'Tidak ada',
        'rekomendasi' => false
    ]
];

// Ikon sementara untuk setiap menu.
$ikonMenu = [
    'M001' => '🍛',
    'M002' => '🍜',
    'M003' => '🍗',
    'M004' => '🥤',
    'M005' => '☕',
    'M006' => '🥑'
];

// ==========================================
// 5. MENYIMPAN STOK AWAL
// ==========================================

if (!isset($app['stok_menu'])) {
    $app['stok_menu'] = [];

    foreach ($dataAwalMenu as $id => $data) {
        $app['stok_menu'][$id] = $data['stok'];
    }
}

// ==========================================
// 6. INSTANSIASI OBJEK MENU
// ==========================================

$menuCafe = [];

foreach ($dataAwalMenu as $id => $data) {
    $stokSaatIni = (int) (
        $app['stok_menu'][$id] ?? $data['stok']
    );

    $status = $stokSaatIni > 0
        ? 'Tersedia'
        : 'Tidak tersedia';

    $menuCafe[$id] = new MenuCafe(
        $id,
        $data['nama'],
        $data['harga'],
        $stokSaatIni,
        $data['deskripsi'],
        $data['kategori'],
        $data['ukuran'],
        $data['pedas'],
        $status,
        $data['rekomendasi']
    );
}

// ==========================================
// 7. PROSES PEMESANAN
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPilihan = $_POST['id_menu'] ?? '';
    $jumlahInput = $_POST['jumlah'] ?? '';
    $token = $_POST['token'] ?? '';

    $pesan = '';
    $jenisPesan = 'error';

    if (
        !is_string($token) ||
        !hash_equals($app['token'], $token)
    ) {
        $pesan =
            'Form tidak berlaku atau sudah digunakan. Silakan coba lagi.';
    } elseif (
        !is_string($idPilihan) ||
        !isset($menuCafe[$idPilihan])
    ) {
        $pesan = 'Silakan pilih menu yang valid.';
    } elseif (!is_string($jumlahInput)) {
        $pesan = 'Format jumlah pesanan tidak valid.';
    } else {
        $jumlah = filter_var(
            $jumlahInput,
            FILTER_VALIDATE_INT
        );

        if ($jumlah === false || $jumlah <= 0) {
            $pesan =
                'Jumlah pesanan harus berupa bilangan bulat lebih dari 0.';
        } else {
            $menuDipilih = $menuCafe[$idPilihan];

            if (
                !$menuDipilih->cekKetersediaanPesanan($jumlah)
            ) {
                $pesan =
                    'Pesanan gagal. Stok menu tidak mencukupi atau menu tidak tersedia.';
            } else {
                $subtotal =
                    $menuDipilih->hitungSubtotal($jumlah);

                if ($menuDipilih->kurangiStok($jumlah)) {
                    // Menyimpan stok terbaru.
                    $app['stok_menu'][$idPilihan] =
                        $menuDipilih->getStok();

                    // Menyimpan ringkasan pesanan.
                    $app['hasil_terakhir'] = [
                        'nama' =>
                            $menuDipilih->getNamaProduk(),

                        'harga' =>
                            $menuDipilih->getHarga(),

                        'jumlah' =>
                            $jumlah,

                        'subtotal' =>
                            $subtotal,

                        'sisa_stok' =>
                            $menuDipilih->getStok(),

                        'waktu' =>
                            date('d-m-Y H:i:s')
                    ];

                    // Mengganti token setelah berhasil.
                    $app['token'] =
                        bin2hex(random_bytes(32));

                    $pesan = 'Pesanan berhasil diproses!';
                    $jenisPesan = 'success';
                } else {
                    $pesan = 'Pesanan gagal diproses.';
                }
            }
        }
    }

    // Pesan sementara untuk halaman berikutnya.
    $app['flash'] = [
        'pesan' => $pesan,
        'jenis' => $jenisPesan,

        'id' => is_string($idPilihan)
            ? $idPilihan
            : '',

        'jumlah' => is_string($jumlahInput)
            ? $jumlahInput
            : ''
    ];

    // Mencegah refresh mengirim ulang pesanan.
    header('Location: index.php', true, 303);
    exit;
}

// ==========================================
// 8. MEMBACA HASIL PROSES
// ==========================================

$flash = $app['flash'] ?? [];

unset($app['flash']);

$pesan = $flash['pesan'] ?? '';
$jenisPesan = $flash['jenis'] ?? '';
$idPilihan = $flash['id'] ?? '';
$jumlahInput = $flash['jumlah'] ?? '';

$hasilPesanan = $app['hasil_terakhir'] ?? null;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>AH Master Wash Cafe</title>

    <style>
        /* =====================================
           1. WARNA UTAMA
           Ubah warna di sini untuk menyesuaikan.
        ===================================== */

        :root {
            --purple: #401553;
            --purple-dark: #2c1039;
            --lavender: #bb94df;
            --stripe: #cab0d0;
            --yellow: #ffeb54;
            --card: #efedf0;
            --ink: #351044;
            --muted: #65546d;

            --shadow:
                0 5px 0 rgba(64, 21, 83, 0.14);
        }

        /* =====================================
           2. PENGATURAN DASAR
        ===================================== */

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;

            font-family:
                "Trebuchet MS",
                Arial,
                sans-serif;

            color: var(--ink);
            background: #221729;
        }

        button,
        input,
        select {
            font: inherit;
        }

        button,
        a,
        input,
        select,
        summary {
            -webkit-tap-highlight-color: transparent;
        }

        button {
            cursor: pointer;
        }

        button:focus-visible,
        a:focus-visible,
        input:focus-visible,
        select:focus-visible,
        summary:focus-visible {
            outline: 3px solid var(--purple);
            outline-offset: 4px;
        }

        [hidden] {
            display: none !important;
        }

        /* =====================================
           3. PANEL APLIKASI DAN LATAR BERGARIS
        ===================================== */

        .app {
            max-width: 540px;
            min-height: 100vh;
            margin: auto;
            padding-bottom: 28px;

            background: repeating-linear-gradient(
                90deg,
                var(--lavender) 0,
                var(--lavender) 57px,
                var(--stripe) 57px,
                var(--stripe) 100px
            );
        }

        /* =====================================
           4. HEADER
        ===================================== */

        header {
            background: var(--purple);
            padding: 17px 22px;

            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;

            color: var(--yellow);
            border-radius: 0 0 22px 22px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;

            color: inherit;
            text-decoration: none;
        }

        .brand-icon {
            display: grid;
            place-items: center;

            width: 42px;
            height: 42px;

            background: var(--yellow);
            color: var(--ink);

            border: 1px solid var(--ink);
            border-radius: 50%;

            font-size: 25px;
        }

        .brand strong {
            display: block;
            font-size: 14px;
            line-height: 1.12;
        }

        .brand small {
            font-size: 10px;
            letter-spacing: 1px;
        }

        .header-link {
            display: grid;
            place-items: center;

            width: 38px;
            height: 38px;

            border: 1px solid var(--yellow);
            border-radius: 50%;

            color: var(--yellow);
            text-decoration: none;
            font-size: 23px;
        }

        /* =====================================
           5. JUDUL HALAMAN
        ===================================== */

        main {
            padding: 25px 20px 0;
        }

        .eyebrow {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 2px;
            margin: 0 0 9px;
        }

        h1 {
            max-width: 380px;
            margin: 0 0 10px;

            font-size: 28px;
            font-weight: 900;
            line-height: 1.08;
            letter-spacing: -1px;
        }

        .intro {
            font-size: 12px;
            line-height: 1.6;
            margin: 0 0 22px;
            color: #4c2b5b;
        }

        /* =====================================
           6. KOLOM PENCARIAN
        ===================================== */

        .search-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;

            flex: 1;
            min-width: 0;
            min-height: 48px;
            padding: 0 14px;

            background: var(--card);
            border: 1px solid white;
            border-radius: 30px;

            box-shadow: var(--shadow);
        }

        .search-icon {
            font-size: 22px;
            color: var(--purple);
        }

        .search-box input {
            width: 100%;
            min-width: 0;
            padding: 14px 0;

            border: 0;
            outline: none;
            background: transparent;

            color: var(--ink);
            font-size: 12px;
        }

        .search-box:focus-within {
            outline: 3px solid var(--purple);
            outline-offset: 2px;
        }

        .search-box input:focus-visible {
            outline: 0;
        }

        .search-box input::placeholder {
            color: #705779;
        }

        .sort-button {
            flex-shrink: 0;

            width: 46px;
            height: 46px;

            border: 1px solid var(--purple);
            border-radius: 50%;

            background: var(--lavender);
            color: var(--purple);

            box-shadow: var(--shadow);
            font-size: 23px;
        }

        .sort-button[aria-pressed="true"] {
            background: var(--yellow);
        }

        /* =====================================
           7. TOMBOL KATEGORI
        ===================================== */

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;

            margin: 22px 0 27px;
        }

        .filter {
            padding: 9px 13px;

            border: 1px solid var(--purple);
            border-radius: 24px;

            background: var(--card);
            color: var(--ink);

            font-size: 12px;
            font-weight: 800;

            box-shadow:
                0 3px 0 rgba(64, 21, 83, 0.19);
        }

        .filter.active {
            background: var(--purple);
            color: var(--yellow);
        }

        .section-line {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-bottom: 12px;
        }

        .section-line h2 {
            font-size: 15px;
            margin: 0;
        }

        .section-line span {
            font-size: 11px;
            color: #4c2b5b;
        }

        /* =====================================
           8. KARTU MENU DUA KOLOM
        ===================================== */

        .daftar-menu {
            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 16px 13px;
        }

        .menu-card {
            position: relative;
            min-width: 0;

            display: flex;
            flex-direction: column;

            padding: 13px 12px 15px;

            background: var(--card);
            border: 1px solid #ffffff;
            border-radius: 23px;

            box-shadow: var(--shadow);
        }

        .food-art {
            position: relative;

            display: grid;
            place-items: center;

            height: 112px;
        }

        .food-art::before {
            content: "";

            position: absolute;
            width: 95px;
            height: 65px;

            background: #ded8e3;
            border-radius: 50%;

            transform: translateY(15px);
        }

        .food-art span {
            position: relative;

            font-family:
                "Apple Color Emoji",
                "Segoe UI Emoji",
                "Noto Color Emoji",
                sans-serif;

            font-size: 76px;
            line-height: 1;

            filter:
                drop-shadow(0 5px 2px #38204426);
        }

        .menu-card h3 {
            margin: 8px 0 4px;

            font-size: 15px;
            line-height: 1.2;
            letter-spacing: -0.4px;

            text-align: center;
        }

        .description {
            min-height: 28px;
            margin: 0 0 8px;

            font-size: 10px;
            line-height: 1.4;
            text-align: center;

            color: var(--muted);
        }

        .stock {
            margin: 0 0 9px;

            font-size: 10px;
            text-align: center;
            color: var(--muted);
        }

        .card-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 4px;
            margin-top: auto;
        }

        .price {
            font-size: 14px;
            font-weight: 900;
            letter-spacing: -0.4px;
        }

        .add-button {
            flex-shrink: 0;

            width: 32px;
            height: 32px;

            border: 1px solid var(--purple);
            border-radius: 50%;

            background: var(--lavender);
            color: var(--yellow);

            font-size: 27px;
            line-height: 1;

            text-shadow:
                1px 0 var(--purple),
                -1px 0 var(--purple),
                0 1px var(--purple),
                0 -1px var(--purple);

            box-shadow: 0 2px 0 #40155333;
        }

        .add-button:hover {
            background: var(--purple);
        }

        .add-button:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .menu-card details {
            margin-top: 10px;
            font-size: 10px;
            color: var(--muted);
        }

        summary {
            cursor: pointer;
        }

        details p {
            line-height: 1.6;
            margin-bottom: 0;
        }

        .badge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1;

            padding: 4px 7px;

            background: var(--yellow);
            border-radius: 10px;

            font-size: 9px;
            font-weight: bold;
        }

        .empty {
            padding: 20px;
            text-align: center;

            background: var(--card);
            border-radius: 18px;

            font-size: 13px;
        }

        /* =====================================
           9. FORM PEMESANAN
        ===================================== */

        .form-pesanan,
        .hasil {
            margin-top: 28px;
            padding: 22px;

            background: var(--card);
            border: 1px solid white;
            border-radius: 24px;

            box-shadow: var(--shadow);
            scroll-margin-top: 18px;
        }

        .step {
            display: inline-block;
            margin-bottom: 6px;

            font-size: 10px;
            letter-spacing: 1.5px;

            color: var(--muted);
        }

        .form-pesanan h2,
        .hasil h2 {
            margin: 0 0 8px;

            font-size: 22px;
            letter-spacing: -0.7px;
        }

        .form-help {
            margin: 0 0 15px;

            font-size: 12px;
            line-height: 1.5;

            color: var(--muted);
        }

        label.field {
            display: block;
            margin: 16px 0 7px;

            font-size: 12px;
            font-weight: bold;
        }

        select,
        input[type="number"] {
            width: 100%;
            padding: 13px;

            border: 1px solid #cabfd1;
            border-radius: 14px;

            background: white;
            color: var(--ink);

            font-size: 13px;
        }

        select {
            white-space: normal;
        }

        button.submit {
            width: 100%;
            margin-top: 20px;
            padding: 13px;

            border: 1px solid var(--purple);
            border-radius: 24px;

            background: var(--yellow);
            color: var(--ink);

            font-weight: bold;

            box-shadow: 0 3px 0 #40155322;
        }

        .submit:hover {
            background: #ffe427;
        }

        /* =====================================
           10. NOTIFIKASI
        ===================================== */

        .notifikasi {
            margin: 14px 0;
            padding: 13px;

            border-radius: 14px;

            font-size: 12px;
            line-height: 1.5;
        }

        .success {
            background: #e2f3da;
            color: #254d1a;
        }

        .error {
            background: #ffe0e5;
            color: #81223b;
        }

        /* =====================================
           11. RINGKASAN PESANAN
        ===================================== */

        .receipt-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;

            padding: 9px 0;
            border-bottom: 1px dashed #c8bdcf;

            font-size: 12px;
        }

        .receipt-row span:last-child {
            text-align: right;
            font-weight: bold;
        }

        .receipt-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;

            margin-top: 15px;
            padding: 14px;

            background: var(--yellow);
            border-radius: 15px;

            font-size: 17px;
            font-weight: bold;
        }

        .receipt-note {
            font-size: 10px;
            line-height: 1.5;
            color: var(--muted);
        }

        footer {
            padding: 28px 22px 0;

            font-size: 10px;
            line-height: 1.7;
            text-align: center;

            color: #573266;
        }

        /* =====================================
           12. PENYESUAIAN UKURAN LAYAR
        ===================================== */

        @media (min-width: 600px) {
            body {
                padding: 24px 0;
            }

            .app {
                overflow: hidden;
                border-radius: 26px;
                box-shadow: 0 20px 70px #0005;
            }

            h1 {
                font-size: 32px;
            }

            .food-art {
                height: 135px;
            }

            .food-art span {
                font-size: 88px;
            }

            .menu-card h3 {
                font-size: 17px;
            }

            .description {
                font-size: 11px;
            }

            .price {
                font-size: 16px;
            }
        }

        @media (max-width: 350px) {
            main {
                padding-left: 12px;
                padding-right: 12px;
            }

            .filters {
                gap: 6px;
            }

            .filter {
                padding: 8px 10px;
                font-size: 11px;
            }

            .menu-card {
                padding: 10px;
            }

            .price {
                font-size: 12px;
            }

            .menu-card h3 {
                font-size: 13px;
            }
        }

        @media (prefers-reduced-motion: no-preference) {
            button {
                transition:
                    background 0.15s,
                    transform 0.15s;
            }

            button:active {
                transform: translateY(2px);
            }
        }
    </style>
</head>

<body>
<div class="app">

    <!-- HEADER -->
    <header>
        <a
            class="brand"
            href="index.php"
            aria-label="AH Master Wash Cafe, beranda"
        >
            <span
                class="brand-icon"
                aria-hidden="true"
            >
                ☕
            </span>

            <span>
                <strong>AH Master</strong>
                <small>WASH CAFE</small>
            </span>
        </a>

        <a
            class="header-link"
            href="#form-pesanan"
            aria-label="Buka form pemesanan"
        >
            ↗
        </a>
    </header>

    <main>
        <!-- JUDUL -->
        <p class="eyebrow">
            MAKAN ENAK, MOOD NAIK!
        </p>

        <h1>
            Pilih<br>
            Makanan dan Minuman<br>
            Favoritmu!
        </h1>

        <p class="intro">
            Temukan favoritmu, pilih jumlahnya,
            dan pesan sekarang.
        </p>

        <!-- PENCARIAN DAN PENGURUTAN -->
        <div class="search-row">

            <label class="search-box">
                <span
                    class="search-icon"
                    aria-hidden="true"
                >
                    ⌕
                </span>

                <input
                    type="search"
                    id="cari-menu"
                    placeholder="Cari makanan / minuman / snack"
                    aria-label="Cari menu"
                >
            </label>

            <button
                class="sort-button"
                type="button"
                id="urut-harga"
                aria-label="Urutkan harga termurah"
                title="Urutkan harga termurah"
                aria-pressed="false"
            >
                ⇄
            </button>

        </div>

        <!-- FILTER KATEGORI -->
        <div
            class="filters"
            role="group"
            aria-label="Kategori menu"
        >
            <button
                class="filter active"
                type="button"
                data-category="Semua"
                aria-pressed="true"
            >
                Semua
            </button>

            <button
                class="filter"
                type="button"
                data-category="Makanan"
                aria-pressed="false"
            >
                Makanan
            </button>

            <button
                class="filter"
                type="button"
                data-category="Minuman"
                aria-pressed="false"
            >
                Minuman
            </button>

            <button
                class="filter"
                type="button"
                data-category="Snack"
                aria-pressed="false"
            >
                Snack
            </button>
        </div>

        <!-- INFORMASI JUMLAH MENU -->
        <div class="section-line">
            <h2>Menu pilihan</h2>

            <span
                id="jumlah-menu"
                aria-live="polite"
            >
                <?= count($menuCafe) ?> menu
            </span>
        </div>

        <!-- KARTU MENU -->
        <div
            class="daftar-menu"
            id="daftar-menu"
        >
            <?php foreach ($menuCafe as $id => $menu): ?>

                <article
                    class="menu-card"
                    data-name="<?= h($menu->getNamaProduk()) ?>"
                    data-category="<?= h($menu->getKategori()) ?>"
                    data-price="<?= $menu->getHarga() ?>"
                >
                    <?php if ($menu->getRekomendasi()): ?>

                        <span class="badge">
                            ★ Favorit
                        </span>

                    <?php endif; ?>

                    <div
                        class="food-art"
                        aria-hidden="true"
                    >
                        <span>
                            <?= $ikonMenu[$id] ?? '🍽️' ?>
                        </span>
                    </div>

                    <h3>
                        <?= h($menu->getNamaProduk()) ?>
                    </h3>

                    <p class="description">
                        <?= h($menu->getDeskripsi()) ?>
                    </p>

                    <p class="stock">
                        Stok <?= $menu->getStok() ?>

                        ·

                        <?= h($menu->getStatusMenu()) ?>
                    </p>

                    <div class="card-bottom">

                        <span class="price">
                            <?= rupiah($menu->getHarga()) ?>
                        </span>

                        <button
                            class="add-button"
                            type="button"
                            data-menu="<?= h($id) ?>"
                            aria-label="Pilih <?= h($menu->getNamaProduk()) ?>"

                            <?= !$menu->cekKetersediaanPesanan(1)
                                ? 'disabled'
                                : '' ?>
                        >
                            +
                        </button>

                    </div>

                    <details>
                        <summary>
                            Detail menu
                        </summary>

                        <p>
                            <?= nl2br(
                                h(
                                    $menu->tampilkanDetailMenu()
                                )
                            ) ?>
                        </p>
                    </details>

                </article>

            <?php endforeach; ?>
        </div>

        <!-- HASIL PENCARIAN KOSONG -->
        <p
            class="empty"
            id="menu-kosong"
            hidden
        >
            Belum ada menu yang sesuai.
            Coba kategori atau kata kunci lain.
        </p>

        <noscript>
            <p class="empty">
                Pilih menu melalui form di bawah.
                Pencarian dan tombol + memerlukan JavaScript.
            </p>
        </noscript>

        <!-- FORM PEMESANAN -->
        <section
            class="form-pesanan"
            id="form-pesanan"
        >
            <span class="step">
                SUDAH MENEMUKAN FAVORITMU?
            </span>

            <h2>Pesan di sini</h2>

            <p class="form-help">
                Pilih satu menu dan jumlah yang kamu inginkan.
            </p>

            <?php if ($pesan !== ''): ?>

                <div
                    class="notifikasi <?= h($jenisPesan) ?>"
                    role="status"
                >
                    <?= h($pesan) ?>
                </div>

            <?php endif; ?>

            <form method="POST" action="index.php">

                <input
                    type="hidden"
                    name="token"
                    value="<?= h($app['token']) ?>"
                >

                <label
                    class="field"
                    for="id_menu"
                >
                    Pilihan menu
                </label>

                <select
                    name="id_menu"
                    id="id_menu"
                    required
                >
                    <option value="">
                        Pilih makanan atau minuman
                    </option>

                    <?php foreach ($menuCafe as $id => $menu): ?>

                        <option
                            value="<?= h($id) ?>"

                            <?= !$menu->cekKetersediaanPesanan(1)
                                ? 'disabled'
                                : '' ?>

                            <?= $idPilihan === $id
                                ? 'selected'
                                : '' ?>
                        >
                            <?= h($menu->getNamaProduk()) ?>

                            ·

                            <?= rupiah($menu->getHarga()) ?>

                            (stok <?= $menu->getStok() ?>)
                        </option>

                    <?php endforeach; ?>

                </select>

                <label
                    class="field"
                    for="jumlah"
                >
                    Jumlah pesanan
                </label>

                <input
                    type="number"
                    name="jumlah"
                    id="jumlah"
                    min="1"
                    step="1"
                    placeholder="Contoh: 2"
                    required
                    value="<?= h($jumlahInput) ?>"
                >

                <button
                    class="submit"
                    type="submit"
                >
                    Pesan sekarang ↗
                </button>

            </form>
        </section>

        <!-- RINGKASAN PESANAN -->
        <?php if ($hasilPesanan !== null): ?>

            <section class="hasil">

                <span class="step">
                    PESANAN TERAKHIR
                </span>

                <h2>
                    Favoritmu sudah dipesan!
                </h2>

                <p class="receipt-note">
                    <?= h($hasilPesanan['waktu']) ?>
                </p>

                <div class="receipt-row">
                    <span>Menu</span>

                    <span>
                        <?= h($hasilPesanan['nama']) ?>
                    </span>
                </div>

                <div class="receipt-row">
                    <span>Harga satuan</span>

                    <span>
                        <?= rupiah($hasilPesanan['harga']) ?>
                    </span>
                </div>

                <div class="receipt-row">
                    <span>Jumlah</span>

                    <span>
                        <?= $hasilPesanan['jumlah'] ?>
                    </span>
                </div>

                <div class="receipt-row">
                    <span>Sisa stok saat dipesan</span>

                    <span>
                        <?= $hasilPesanan['sisa_stok'] ?>
                    </span>
                </div>

                <div class="receipt-total">
                    <span>Total</span>

                    <span>
                        <?= rupiah($hasilPesanan['subtotal']) ?>
                    </span>
                </div>

            </section>

        <?php endif; ?>

    </main>

    <footer>
        © <?= date('Y') ?> AH Master Wash Cafe<br>
        Demo pemesanan · stok tersimpan per sesi browser
    </footer>

</div>

<script>
(() => {
    // ======================================
    // 1. MENGAMBIL ELEMEN HALAMAN
    // ======================================

    const cards = [
        ...document.querySelectorAll('.menu-card')
    ];

    const list =
        document.getElementById('daftar-menu');

    const search =
        document.getElementById('cari-menu');

    const sort =
        document.getElementById('urut-harga');

    let category = 'Semua';
    let cheapest = false;

    // ======================================
    // 2. FILTER DAN PENGURUTAN MENU
    // ======================================

    function update() {
        const query = search.value
            .trim()
            .toLocaleLowerCase('id');

        let count = 0;

        cards.forEach(card => {
            const categoryMatch =
                category === 'Semua' ||
                card.dataset.category === category;

            const nameMatch = card.dataset.name
                .toLocaleLowerCase('id')
                .includes(query);

            const match =
                categoryMatch && nameMatch;

            card.hidden = !match;

            if (match) {
                count++;
            }
        });

        const ordered = cheapest
            ? [...cards].sort(
                (a, b) =>
                    Number(a.dataset.price) -
                    Number(b.dataset.price)
            )
            : cards;

        ordered.forEach(card => {
            list.appendChild(card);
        });

        document.getElementById('jumlah-menu')
            .textContent = count + ' menu';

        document.getElementById('menu-kosong')
            .hidden = count > 0;
    }

    // ======================================
    // 3. TOMBOL KATEGORI
    // ======================================

    document.querySelectorAll('.filter')
        .forEach(button => {
            button.addEventListener('click', () => {
                category = button.dataset.category;

                document.querySelectorAll('.filter')
                    .forEach(other => {
                        const active =
                            other === button;

                        other.classList.toggle(
                            'active',
                            active
                        );

                        other.setAttribute(
                            'aria-pressed',
                            String(active)
                        );
                    });

                update();
            });
        });

    // ======================================
    // 4. PENCARIAN NAMA MENU
    // ======================================

    search.addEventListener('input', update);

    // ======================================
    // 5. URUTKAN HARGA TERMURAH
    // ======================================

    sort.addEventListener('click', () => {
        cheapest = !cheapest;

        sort.setAttribute(
            'aria-pressed',
            String(cheapest)
        );

        sort.setAttribute(
            'aria-label',
            cheapest
                ? 'Kembalikan urutan awal'
                : 'Urutkan harga termurah'
        );

        sort.title =
            sort.getAttribute('aria-label');

        update();
    });

    // ======================================
    // 6. TOMBOL + MEMILIH MENU PADA FORM
    // ======================================

    document.querySelectorAll('.add-button')
        .forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('id_menu')
                    .value = button.dataset.menu;

                const quantity =
                    document.getElementById('jumlah');

                quantity.value = '1';

                document.getElementById('form-pesanan')
                    .scrollIntoView({
                        behavior: 'auto',
                        block: 'start'
                    });

                quantity.focus({
                    preventScroll: true
                });
            });
        });
})();
</script>

</body>
</html>