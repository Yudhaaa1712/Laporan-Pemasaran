<?php
require_once '../../config/database.php';
require_once '../../functions/product_functions.php';

// Cek apakah ada parameter id
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

// Dapatkan data penjualan sebelum dihapus untuk mengembalikan stok
$sale_query = "SELECT * FROM sales WHERE id = '$id'";
$sale_result = mysqli_query($conn, $sale_query);

if (mysqli_num_rows($sale_result) == 0) {
    header('Location: index.php');
    exit;
}

$sale = mysqli_fetch_assoc($sale_result);
$product_id = $sale['product_id'];
$quantity = $sale['quantity'];

// Dapatkan data produk
$product = getProductById($conn, $product_id);

// Kembalikan stok produk
$updated_stock = $product['stock'] + $quantity;
$update_stock = "UPDATE products SET stock = '$updated_stock' WHERE id = '$product_id'";
mysqli_query($conn, $update_stock);

// Hapus data penjualan
$delete_query = "DELETE FROM sales WHERE id = '$id'";

if (mysqli_query($conn, $delete_query)) {
    header('Location: index.php?deleted=1');
} else {
    header('Location: index.php?error=1');
}
exit;
?>