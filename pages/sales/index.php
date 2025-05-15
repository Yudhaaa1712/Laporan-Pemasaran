<?php
require_once '../../config/database.php';
require_once '../../functions/product_functions.php';

session_start();

// Cek jika belum login, redirect ke halaman login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /freedom_cofe/pages/login.php');
    exit;
}
// Query untuk mendapatkan data penjualan
$sales_query = "SELECT s.id, p.name as product_name, s.quantity, s.total_price, s.sale_date 
                FROM sales s
                JOIN products p ON s.product_id = p.id
                ORDER BY s.sale_date DESC";
$sales_result = mysqli_query($conn, $sales_query);

include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="bg-white p-4 rounded shadow mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Data Penjualan</h2>
            <a href="add.php" class="bg-amber-600 text-white px-4 py-2 rounded">Tambah Penjualan</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-amber-100">
                        <th class="border p-2 text-left">Produk</th>
                        <th class="border p-2 text-left">Jumlah</th>
                        <th class="border p-2 text-left">Total Harga</th>
                        <th class="border p-2 text-left">Tanggal</th>
                        <th class="border p-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($sales_result) > 0) {
                        while ($row = mysqli_fetch_assoc($sales_result)) {
                            $date = date('d-m-Y', strtotime($row['sale_date']));
                    ?>
                    <tr>
                        <td class="border p-2"><?php echo $row['product_name']; ?></td>
                        <td class="border p-2"><?php echo $row['quantity']; ?></td>
                        <td class="border p-2">Rp <?php echo number_format($row['total_price'], 0, ',', '.'); ?></td>
                        <td class="border p-2"><?php echo $date; ?></td>
                        <td class="border p-2">
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="bg-blue-500 text-white px-2 py-1 rounded text-sm mr-1">Edit</a>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="bg-red-500 text-white px-2 py-1 rounded text-sm" onclick="return confirm('Yakin ingin menghapus data penjualan ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="5" class="border p-2 text-center">Belum ada data penjualan.</td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>