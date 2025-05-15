<?php
require_once '../../config/database.php';
require_once '../../functions/product_functions.php';

$message = '';
$error = '';

// Mendapatkan semua produk untuk dropdown
$products = getAllProducts($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $sale_date = $_POST['sale_date'] ?? '';
    
    // Validasi input
    if (empty($product_id) || empty($quantity) || empty($sale_date)) {
        $error = "Semua field wajib diisi!";
    } else {
        // Mendapatkan harga produk
        $product = getProductById($conn, $product_id);
        if ($product) {
            $total_price = $product['price'] * $quantity;
            
            // Insert data penjualan
            $product_id = mysqli_real_escape_string($conn, $product_id);
            $quantity = mysqli_real_escape_string($conn, $quantity);
            $total_price = mysqli_real_escape_string($conn, $total_price);
            $sale_date = mysqli_real_escape_string($conn, $sale_date);
            
            $sql = "INSERT INTO sales (product_id, quantity, total_price, sale_date) 
                    VALUES ('$product_id', '$quantity', '$total_price', '$sale_date')";
            
            if (mysqli_query($conn, $sql)) {
                // Update stok produk
                $new_stock = $product['stock'] - $quantity;
                $update_stock = "UPDATE products SET stock = '$new_stock' WHERE id = '$product_id'";
                mysqli_query($conn, $update_stock);
                
                $message = "Data penjualan berhasil ditambahkan!";
            } else {
                $error = "Gagal menambahkan data penjualan: " . mysqli_error($conn);
            }
        } else {
            $error = "Produk tidak ditemukan!";
        }
    }
}

include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="bg-white p-4 rounded shadow mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Tambah Data Penjualan</h2>
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
                <label for="product_id" class="block text-gray-700 font-bold mb-2">Produk</label>
                <select id="product_id" name="product_id" required
                        class="w-full px-3 py-2 border rounded-md">
                    <option value="">-- Pilih Produk --</option>
                    <?php foreach ($products as $product) { ?>
                        <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>" data-stock="<?php echo $product['stock']; ?>">
                            <?php echo $product['name']; ?> - Rp <?php echo number_format($product['price'], 0, ',', '.'); ?> (Stok: <?php echo $product['stock']; ?>)
                        </option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="quantity" class="block text-gray-700 font-bold mb-2">Jumlah</label>
                <input type="number" id="quantity" name="quantity" min="1" required
                       class="w-full px-3 py-2 border rounded-md">
                <p id="stock-warning" class="hidden text-red-500 text-sm mt-1">Jumlah melebihi stok yang tersedia!</p>
            </div>
            
            <div class="mb-4">
                <label for="sale_date" class="block text-gray-700 font-bold mb-2">Tanggal Penjualan</label>
                <input type="date" id="sale_date" name="sale_date" required
                       class="w-full px-3 py-2 border rounded-md" value="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Total Harga</label>
                <div id="total_price" class="w-full px-3 py-2 border rounded-md bg-gray-100">Rp 0</div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" id="submit-btn" class="bg-amber-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</main>

<script>
    // Script untuk menghitung total harga secara otomatis
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const totalPrice = document.getElementById('total_price');
    const stockWarning = document.getElementById('stock-warning');
    const submitBtn = document.getElementById('submit-btn');
    
    function calculateTotal() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        if (selectedOption.value !== "") {
            const price = parseFloat(selectedOption.getAttribute('data-price'));
            const stock = parseInt(selectedOption.getAttribute('data-stock'));
            const quantity = parseInt(quantityInput.value) || 0;
            
            if (quantity > stock) {
                stockWarning.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                stockWarning.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
            
            const total = price * quantity;
            totalPrice.textContent = `Rp ${total.toLocaleString('id-ID')}`;
        } else {
            totalPrice.textContent = 'Rp 0';
        }
    }
    
    productSelect.addEventListener('change', calculateTotal);
    quantityInput.addEventListener('input', calculateTotal);
</script>

<?php include '../../includes/footer.php'; ?>