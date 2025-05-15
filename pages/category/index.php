<?php
require_once '../../config/database.php';
require_once '../../functions/category_functions.php';

session_start();

// Cek jika belum login, redirect ke halaman login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /freedom_cofe/pages/login.php');
    exit;
}

// Mendapatkan semua kategori
$category_counts = getProductCountByCategory($conn);

// Pesan sukses/error
$message = '';
$error = '';

if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $message = "Kategori berhasil dihapus!";
}
if (isset($_GET['error']) && $_GET['error'] == 1) {
    $error = "Gagal menghapus kategori!";
}

include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="bg-white p-4 rounded shadow mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Daftar Kategori</h2>
            <a href="add.php" class="bg-amber-600 text-white px-4 py-2 rounded">Tambah Kategori</a>
        </div>
        
        <?php if (!empty($message)) { ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $message; ?>
            </div>
        <?php } ?>
        
        <?php if (!empty($error)) { ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php } ?>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-amber-100">
                        <th class="border p-2 text-left">Nama Kategori</th>
                        <th class="border p-2 text-left">Deskripsi</th>
                        <th class="border p-2 text-left">Jumlah Produk</th>
                        <th class="border p-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($category_counts as $category) { ?>
                    <tr>
                        <td class="border p-2"><?php echo htmlspecialchars($category['name']); ?></td>
                        <td class="border p-2">
                            <?php 
                            // Perbaikan untuk deskripsi yang bisa null
                            $description = isset($category['description']) ? $category['description'] : '';
                            echo htmlspecialchars(substr($description, 0, 50) . (strlen($description) > 50 ? '...' : ''));
                            ?>
                        </td>
                        <td class="border p-2"><?php echo $category['product_count']; ?></td>
                        <td class="border p-2">
                            <a href="edit.php?id=<?php echo $category['id']; ?>" class="bg-blue-500 text-white px-2 py-1 rounded text-sm mr-1">Edit</a>
                            <a href="delete.php?id=<?php echo $category['id']; ?>" class="bg-red-500 text-white px-2 py-1 rounded text-sm" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if (count($category_counts) == 0) { ?>
                        <tr>
                            <td colspan="4" class="border p-2 text-center">Belum ada data kategori.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>