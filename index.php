<?php
// Mulai output buffering agar header() bisa dipanggil setelah output
ob_start();

require 'includes/header.php';
require 'includes/navbar.php';

$page = $_GET['page'] ?? 'home';
$page = strtolower(trim($page));

$allow = [
    'home',
    'profile',
    'bidang',
    'layanan',
    'detail_layanan',
    'berita',
    'detail_berita',
    'kontak',
    'tracking',
    'cari',
    'form'
];

if (in_array($page, $allow)) {
    require __DIR__ . '/pages/' . $page . '.php';
} else {
    ?>
    <div class="container py-5">
        <div class="alert alert-danger">
            <h3>404</h3>
            Halaman tidak ditemukan
        </div>
    </div>
    <?php
}

require 'includes/footer.php';

// Kirim semua output yang sudah ditampung (otomatis dilakukan oleh PHP,
// kita hanya perlu memastikan buffer dibersihkan jika ada redirect)
// Jika sudah terjadi exit (redirect), bagian ini tidak akan dijalankan.
ob_end_flush();
?>