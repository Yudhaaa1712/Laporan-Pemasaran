<?php
require_once '../../config/database.php';
require_once '../../functions/sales_functions.php';

session_start();

// Cek jika belum login, redirect ke halaman login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /freedom_cofe/pages/login.php');
    exit;
}

// Set default tanggal (30 hari terakhir)
$end_date = date('Y-m-d');
$start_date = date('Y-m-d', strtotime('0 days'));

// Jika ada filter tanggal dari form
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

// Mendapatkan data laporan penjualan
$product_sales = getSalesByProduct($conn, $start_date, $end_date);
$daily_sales = getSalesByDate($conn, $start_date, $end_date);

// Query untuk laporan penjualan per kategori
$category_sales_query = "SELECT c.name as category_name, SUM(s.quantity) as total_quantity, 
                       SUM(s.total_price) as total_sales
                       FROM sales s
                       JOIN products p ON s.product_id = p.id
                       JOIN categories c ON p.category_id = c.id
                       WHERE s.sale_date BETWEEN '$start_date' AND '$end_date'
                       GROUP BY p.category_id
                       ORDER BY total_sales DESC";
$category_sales_result = mysqli_query($conn, $category_sales_query);

include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<main class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Laporan Penjualan</h1>
    
   <!-- Filter Tanggal -->
<div class="bg-white p-4 rounded shadow mb-8">
    <h2 class="text-lg font-bold mb-4">Filter Tanggal</h2>
    <form action="" method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label for="start_date" class="block text-gray-700 font-bold mb-2">Tanggal Mulai</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>"
                   class="px-3 py-2 border rounded-md">
        </div>
        <div>
            <label for="end_date" class="block text-gray-700 font-bold mb-2">Tanggal Akhir</label>
            <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>"
                   class="px-3 py-2 border rounded-md">
        </div>
        <div>
            <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded">Filter</button>
        </div>
    </form>
    
    <!-- Tombol Ekspor -->
    <div class="mt-4 border-t pt-4">
        <h3 class="text-md font-bold mb-2">Ekspor Data</h3>
        <div class="flex flex-wrap gap-2">
            <a href="export_excel.php?type=product&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" 
               class="bg-green-600 text-white px-3 py-2 rounded text-sm flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m-9 6h14a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Ekspor Laporan Per Produk
            </a>
            <a href="export_excel.php?type=category&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" 
               class="bg-green-600 text-white px-3 py-2 rounded text-sm flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m-9 6h14a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Ekspor Laporan Per Kategori
            </a>
            <a href="export_excel.php?type=date&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" 
               class="bg-green-600 text-white px-3 py-2 rounded text-sm flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m-9 6h14a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Ekspor Laporan Per Tanggal
            </a>
        </div>
    </div>
</div>
    
    <!-- Ringkasan Penjualan -->
    <div class="bg-white p-4 rounded shadow mb-8">
        <h2 class="text-lg font-bold mb-4">Ringkasan Penjualan (<?php echo date('d-m-Y', strtotime($start_date)); ?> s/d <?php echo date('d-m-Y', strtotime($end_date)); ?>)</h2>
        
        <?php
        $total_sales_amount = 0;
        $total_products_sold = 0;
        
        foreach ($product_sales as $sale) {
            $total_sales_amount += $sale['total_sales'];
            $total_products_sold += $sale['total_quantity'];
        }
        ?>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="border p-4 rounded">
                <h3 class="font-semibold text-gray-600">Total Penjualan</h3>
                <p class="text-2xl font-bold text-amber-600">Rp <?php echo number_format($total_sales_amount, 0, ',', '.'); ?></p>
            </div>
            <div class="border p-4 rounded">
                <h3 class="font-semibold text-gray-600">Jumlah Produk Terjual</h3>
                <p class="text-2xl font-bold text-amber-600"><?php echo $total_products_sold; ?> item</p>
            </div>
            <div class="border p-4 rounded">
                <h3 class="font-semibold text-gray-600">Periode</h3>
                <p class="text-2xl font-bold text-amber-600"><?php echo count($daily_sales); ?> hari</p>
            </div>
        </div>
    </div>
    
    <!-- Laporan Penjualan per Produk -->
    <div class="bg-white p-4 rounded shadow mb-8">
        <h2 class="text-xl font-bold mb-4">Penjualan per Produk</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-amber-100">
                        <th class="border p-2 text-left">Nama Produk</th>
                        <th class="border p-2 text-left">Jumlah Terjual</th>
                        <th class="border p-2 text-left">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($product_sales) > 0) {
                        foreach ($product_sales as $sale) {
                    ?>
                    <tr>
                        <td class="border p-2"><?php echo $sale['product_name']; ?></td>
                        <td class="border p-2"><?php echo $sale['total_quantity']; ?></td>
                        <td class="border p-2">Rp <?php echo number_format($sale['total_sales'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php 
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="3" class="border p-2 text-center">Belum ada data penjualan dalam periode ini.</td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Laporan Penjualan per Kategori -->
    <div class="bg-white p-4 rounded shadow mb-8">
        <h2 class="text-xl font-bold mb-4">Penjualan per Kategori</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-amber-100">
                        <th class="border p-2 text-left">Kategori</th>
                        <th class="border p-2 text-left">Jumlah Terjual</th>
                        <th class="border p-2 text-left">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($category_sales_result) > 0) {
                        while ($row = mysqli_fetch_assoc($category_sales_result)) {
                    ?>
                    <tr>
                        <td class="border p-2"><?php echo $row['category_name']; ?></td>
                        <td class="border p-2"><?php echo $row['total_quantity']; ?></td>
                        <td class="border p-2">Rp <?php echo number_format($row['total_sales'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php 
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="3" class="border p-2 text-center">Belum ada data penjualan dalam periode ini.</td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Laporan Penjualan per Tanggal -->
    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Penjualan per Tanggal</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-amber-100">
                        <th class="border p-2 text-left">Tanggal</th>
                        <th class="border p-2 text-left">Jumlah Terjual</th>
                        <th class="border p-2 text-left">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($daily_sales) > 0) {
                        foreach ($daily_sales as $sale) {
                            $date = date('d-m-Y', strtotime($sale['sale_date']));
                    ?>
                    <tr>
                        <td class="border p-2"><?php echo $date; ?></td>
                        <td class="border p-2"><?php echo $sale['total_quantity']; ?></td>
                        <td class="border p-2">Rp <?php echo number_format($sale['total_sales'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php 
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="3" class="border p-2 text-center">Belum ada data penjualan dalam periode ini.</td>
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