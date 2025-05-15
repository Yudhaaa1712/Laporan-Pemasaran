<?php
require_once '../../config/database.php';
require_once '../../functions/product_functions.php';

// Cek apakah ada parameter id
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];
$product = getProductById($conn, $id);

if (!$product) {
    header('Location: index.php');
    exit;
}

// Proses hapus
if (deleteProduct($conn, $id)) {
    // Redirect ke halaman produk dengan pesan sukses
    header('Location: index.php?deleted=1');
} else {
    // Redirect ke halaman produk dengan pesan error
    header('Location: index.php?error=1');
}
exit;
?>