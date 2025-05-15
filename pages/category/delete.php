<?php
require_once '../../config/database.php';
require_once '../../functions/category_functions.php';

session_start();

// Cek jika belum login, redirect ke halaman login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /freedom_cofe/pages/login.php');
    exit;
}

// Cek apakah ada parameter id
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];
$category = getCategoryById($conn, $id);

if (!$category) {
    header('Location: index.php');
    exit;
}

// Proses hapus
if (deleteCategory($conn, $id)) {
    // Redirect ke halaman kategori dengan pesan sukses
    header('Location: index.php?deleted=1');
} else {
    // Redirect ke halaman kategori dengan pesan error
    header('Location: index.php?error=1');
}
exit;
?>