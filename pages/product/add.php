<?php
require_once '../../config/database.php';
require_once '../../functions/product_functions.php';
require_once '../../functions/category_functions.php';

$message = '';
$error = '';

// Mendapatkan semua kategori untuk dropdown
$categories = getAllCategories($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $description = $_POST['description'] ?? '';
    
    // Validasi input
    if (empty($name) || empty($category_id) || empty($price) || empty($stock)) {
        $error = "Semua field wajib diisi!";
    } else {
        // Tambahkan produk
        if (addProduct($conn, $name, $category_id, $price, $stock, $description)) {
            $message = "Produk berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan produk: " . mysqli_error($conn);
        }
    }
}

include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="bg-white p-4 rounded shadow mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Tambah Produk Baru</h2>
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
                <label for="name" class="block text-gray-700 font-bold mb-2">Nama Produk</label>
                <input type="text" id="name" name="name" required
                       class="w-full px-3 py-2 border rounded-md">
            </div>
            
            <div class="mb-4">
                <label for="category_id" class="block text-gray-700 font-bold mb-2">Kategori</label>
                <select id="category_id" name="category_id" required
                        class="w-full px-3 py-2 border rounded-md">
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $category) { ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                        <?php } ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="price" class="block text-gray-700 font-bold mb-2">Harga</label>
                <input type="number" id="price" name="price" required
                       class="w-full px-3 py-2 border rounded-md">
            </div>
            
            <div class="mb-4">
                <label for="stock" class="block text-gray-700 font-bold mb-2">Stok</label>
                <input type="number" id="stock" name="stock" required
                       class="w-full px-3 py-2 border rounded-md">
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-bold mb-2">Deskripsi</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-3 py-2 border rounded-md"></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>