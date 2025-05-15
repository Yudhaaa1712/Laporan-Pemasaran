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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    // Validasi input
    if (empty($name)) {
        $error = "Nama kategori wajib diisi!";
    } else {
        // Update kategori
        if (updateCategory($conn, $id, $name, $description)) {
            $message = "Kategori berhasil diperbarui!";
            $category = getCategoryById($conn, $id); // Refresh data
        } else {
            $error = "Gagal memperbarui kategori: " . mysqli_error($conn);
        }
    }
}

include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="bg-white p-4 rounded shadow mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Edit Kategori</h2>
            <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded">Kembali</a>
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
        
        <form action="" method="POST">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Nama Kategori</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required
                       class="w-full px-3 py-2 border rounded-md">
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-bold mb-2">Deskripsi</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-3 py-2 border rounded-md"><?php echo htmlspecialchars(isset($category['description']) ? $category['description'] : ''); ?></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>