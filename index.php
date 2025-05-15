<?php
require_once 'config/database.php';
require_once 'functions/product_functions.php';
require_once 'functions/category_functions.php';


session_start();

// Cek jika belum login, redirect ke halaman login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /muha/pages/login.php');
    exit;
}


// Mendapatkan data untuk dashboard
$total_products = getTotalProducts($conn);
$best_product = getBestSellingProduct($conn);
$total_sales = getTotalSales($conn);
$categories_with_count = getProductCountByCategory($conn);

include 'includes/header.php';
include 'includes/navbar.php';
?>

<main class="container mx-auto px-4 py-8">
    <!-- Statistik Sederhana -->
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

    <!-- Daftar Produk -->
    <div class="bg-white p-4 rounded shadow mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Daftar Produk</h2>
            <a href="pages/product/add.php" class="bg-amber-600 text-white px-4 py-2 rounded">Tambah Produk</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-amber-100">
                        <th class="border p-2 text-left">Nama Produk</th>
                        <th class="border p-2 text-left">Kategori</th>
                        <th class="border p-2 text-left">Harga</th>
                        <th class="border p-2 text-left">Stok</th>
                        <th class="border p-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $products = getAllProducts($conn);
                    foreach ($products as $product) {
                    ?>
                    <tr>
                        <td class="border p-2"><?php echo $product['name']; ?></td>
                        <td class="border p-2"><?php echo $product['category_name']; ?></td>
                        <td class="border p-2">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                        <td class="border p-2"><?php echo $product['stock']; ?></td>
                        <td class="border p-2">
                            <a href="pages/product/edit.php?id=<?php echo $product['id']; ?>" class="bg-blue-500 text-white px-2 py-1 rounded text-sm mr-1">Edit</a>
                            <a href="pages/product/delete.php?id=<?php echo $product['id']; ?>" class="bg-red-500 text-white px-2 py-1 rounded text-sm" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php
                    }
                    if (count($products) == 0) {
                        echo '<tr><td colspan="5" class="border p-2 text-center">Tidak ada data produk</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Form Pencarian Produk Sederhana -->
    <div class="bg-white p-4 rounded shadow mb-8">
        <h2 class="text-xl font-bold mb-4">Cari Produk</h2>
        <form action="pages/product/index.php" method="GET">
            <div class="flex gap-2">
                <input type="text" name="search" placeholder="Masukkan nama produk" class="border p-2 rounded w-full">
                <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded">Cari</button>
            </div>
        </form>
    </div>

    <!-- Kategori Produk -->
    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Kategori Produk</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <?php foreach ($categories_with_count as $category) { ?>
                <div class="border p-4 rounded text-center">
                    <h3 class="font-bold"><?php echo $category['name']; ?></h3>
                    <p class="text-gray-600"><?php echo $category['product_count']; ?> produk</p>
                </div>
            <?php } ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>