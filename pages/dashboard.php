<?php
require_once '../../config/database.php';
require_once '../../functions/product_functions.php';
require_once '../../functions/category_functions.php';

session_start();

// Cek jika belum login, redirect ke halaman login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /freedom_cofe/pages/login.php');
    exit;
}

// Mendapatkan data untuk dashboard
$total_products = getTotalProducts($conn);
$best_product = getBestSellingProduct($conn);
$total_sales = getTotalSales($conn);

include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold text-lg mb-2">Total Produk</h3>
            <p class="text-2xl text-amber-600"><?php echo $total_products; ?></p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold text-lg mb-2">Produk Terlaris</h3>
            <p class="text-2xl text-amber-600"><?php echo $best_product ? $best_product['name'] : 'Belum ada data'; ?></p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold text-lg mb-2">Total Penjualan</h3>
            <p class="text-2xl text-amber-600">Rp <?php echo number_format($total_sales, 0, ',', '.'); ?></p>
        </div>
    </div>

    <!-- Menampilkan statistik lainnya bisa ditambahkan di sini -->
</main>

<?php include '../../includes/footer.php'; ?>