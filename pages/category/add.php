<?php

require_once '../../config/database.php';
require_once '../../functions/category_functions.php';

session_start();

// Cek jika belum login, redirect ke halaman login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /freedom_cofe/pages/login.php');
    exit;
}

$message = '';
$error = '';
$name = '';
$description = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    // Validasi input
    if (empty($name)) {
        $error = "Nama kategori wajib diisi!";
    } else {
        // Tambahkan kategori
        if (addCategory($conn, $name, $description)) {
            $message = "Kategori berhasil ditambahkan!";
            // Reset form setelah berhasil
            $name = '';
            $description = '';
        } else {
            $error = "Gagal menambahkan kategori: " . mysqli_error($conn);
        }
    }
}

include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="bg-white p-4 rounded shadow mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Tambah Kategori Baru</h2>
            <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded">Kembali</a>
        </div>
        
        <?php if (!empty($message)) { ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $message; ?>
                <p class="mt-2">
                    <a href="index.php" class="text-green-800 underline">Lihat daftar kategori</a> atau 
                    <a href="add.php" class="text-green-800 underline">tambah kategori lain</a>
                </p>
            </div>
        <?php } ?>
        
        <?php if (!empty($error)) { ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php } ?>
        
        <form action="" method="POST">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Nama Kategori</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required
                       class="w-full px-3 py-2 border rounded-md">
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-bold mb-2">Deskripsi</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-3 py-2 border rounded-md"><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>