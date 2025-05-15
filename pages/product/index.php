<?php
require_once '../../config/database.php';
require_once '../../functions/product_functions.php';

session_start();

// Cek jika belum login, redirect ke halaman login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /freedom_cofe/pages/login.php');
    exit;
}

// Handle pencarian
$products = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $products = searchProducts($conn, $_GET['search']);
} else {
    $products = getAllProducts($conn);
}

include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="bg-white p-4 rounded shadow mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Daftar Produk</h2>
            <a href="add.php" class="bg-amber-600 text-white px-4 py-2 rounded">Tambah Produk</a>
        </div>

        <!-- Form Pencarian -->
        <div class="mb-4">
            <form action="" method="GET">
                <div class="flex gap-2">
                    <input type="text" name="search" placeholder="Cari produk..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
                           class="border p-2 rounded w-full">
                    <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded">Cari</button>
                </div>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-amber-100">
                        <th class="border p-2 text-left">Nama Produk</th>
                        <th class="border p-2 text-left">Kategori</th>
                        <th class="border p-2 text-left">Harga</th>
                        <th class="border p-2 text-left">Stok</th>
                        <th class="border p-2 text-left">Deskripsi</th>
                        <th class="border p-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product) { ?>
                    <tr>
                        <td class="border p-2"><?php echo $product['name']; ?></td>
                        <td class="border p-2"><?php echo $product['category_name']; ?></td>
                        <td class="border p-2">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                        <td class="border p-2"><?php echo $product['stock']; ?></td>
                        <td class="border p-2"><?php echo substr($product['description'], 0, 50) . (strlen($product['description']) > 50 ? '...' : ''); ?></td>
                        <td class="border p-2">
                            <a href="edit.php?id=<?php echo $product['id']; ?>" class="bg-blue-500 text-white px-2 py-1 rounded text-sm mr-1">Edit</a>
                            <a href="delete.php?id=<?php echo $product['id']; ?>" class="bg-red-500 text-white px-2 py-1 rounded text-sm" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if (count($products) == 0) { ?>
                        <tr>
                            <td colspan="6" class="border p-2 text-center">
                                <?php echo isset($_GET['search']) ? 'Tidak ada produk yang sesuai dengan pencarian.' : 'Belum ada data produk.'; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>