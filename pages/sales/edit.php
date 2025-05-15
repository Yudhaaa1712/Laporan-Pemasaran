<?php
require_once '../../config/database.php';
require_once '../../functions/product_functions.php';

$message = '';
$error = '';

// Cek apakah ada parameter id
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

// Mendapatkan data penjualan
$sale_query = "SELECT * FROM sales WHERE id = '$id'";
$sale_result = mysqli_query($conn, $sale_query);

if (mysqli_num_rows($sale_result) == 0) {
    header('Location: index.php');
    exit;
}

$sale = mysqli_fetch_assoc($sale_result);
$old_quantity = $sale['quantity'];
$product_id = $sale['product_id'];

// Mendapatkan data produk untuk mengembalikan stok
$product = getProductById($conn, $product_id);

// Mendapatkan semua produk untuk dropdown
$products = getAllProducts($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_product_id = $_POST['product_id'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $sale_date = $_POST['sale_date'] ?? '';
    
    // Validasi input
    if (empty($new_product_id) || empty($quantity) || empty($sale_date)) {
        $error = "Semua field wajib diisi!";
    } else {
        // Jika produk berubah
        if ($new_product_id != $product_id) {
            // Kembalikan stok produk lama
            $updated_old_stock = $product['stock'] + $old_quantity;
            $update_old_stock = "UPDATE products SET stock = '$updated_old_stock' WHERE id = '$product_id'";
            mysqli_query($conn, $update_old_stock);
            
            // Dapatkan produk baru
            $new_product = getProductById($conn, $new_product_id);
            
            // Kurangi stok produk baru
            $updated_new_stock = $new_product['stock'] - $quantity;
            $update_new_stock = "UPDATE products SET stock = '$updated_new_stock' WHERE id = '$new_product_id'";
            mysqli_query($conn, $update_new_stock);
            
            $total_price = $new_product['price'] * $quantity;
        } else {
            // Produk sama, update stok
            $diff = $quantity - $old_quantity;
            $updated_stock = $product['stock'] - $diff;
            $update_stock = "UPDATE products SET stock = '$updated_stock' WHERE id = '$product_id'";
            mysqli_query($conn, $update_stock);
            
            $total_price = $product['price'] * $quantity;
        }
        
        // Update data penjualan
        $new_product_id = mysqli_real_escape_string($conn, $new_product_id);
        $quantity = mysqli_real_escape_string($conn, $quantity);
        $total_price = mysqli_real_escape_string($conn, $total_price);
        $sale_date = mysqli_real_escape_string($conn, $sale_date);
        
        $sql = "UPDATE sales SET product_id = '$new_product_id', quantity = '$quantity', 
                total_price = '$total_price', sale_date = '$sale_date' WHERE id = '$id'";
        
        if (mysqli_query($conn, $sql)) {
            $message = "Data penjualan berhasil diperbarui!";
            
            // Refresh data
            $sale_query = "SELECT * FROM sales WHERE id = '$id'";
            $sale_result = mysqli_query($conn, $sale_query);
            $sale = mysqli_fetch_assoc($sale_result);
            $old_quantity = $sale['quantity'];
            $product_id = $sale['product_id'];
            $product = getProductById($conn, $product_id);
        } else {
            $error = "Gagal memperbarui data penjualan: " . mysqli_error($conn);
        }
    }
}

include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="bg-white p-4 rounded shadow mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Edit Data Penjualan</h2>
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
                    <?php foreach ($products as $p) { ?>
                        <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['price']; ?>" data-stock="<?php echo $p['stock'] + ($p['id'] == $product_id ? $old_quantity : 0); ?>" 
                                <?php echo ($p['id'] == $product_id) ? 'selected' : ''; ?>>
                            <?php echo $p['name']; ?> - Rp <?php echo number_format($p['price'], 0, ',', '.'); ?> 
                            (Stok: <?php echo $p['stock'] + ($p['id'] == $product_id ? $old_quantity : 0); ?>)
                        </option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="quantity" class="block text-gray-700 font-bold mb-2">Jumlah</label>
                <input type="number" id="quantity" name="quantity" min="1" required
                       class="w-full px-3 py-2 border rounded-md" value="<?php echo $sale['quantity']; ?>">
                <p id="stock-warning" class="hidden text-red-500 text-sm mt-1">Jumlah melebihi stok yang tersedia!</p>
            </div>
            
            <div class="mb-4">
                <label for="sale_date" class="block text-gray-700 font-bold mb-2">Tanggal Penjualan</label>
                <input type="date" id="sale_date" name="sale_date" required
                       class="w-full px-3 py-2 border rounded-md" value="<?php echo $sale['sale_date']; ?>">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Total Harga</label>
                <div id="total_price" class="w-full px-3 py-2 border rounded-md bg-gray-100">
                    Rp <?php echo number_format($sale['total_price'], 0, ',', '.'); ?>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" id="submit-btn" class="bg-amber-600 text-white px-4 py-2 rounded">Simpan Perubahan</button>
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
        if (selectedOption) {
            const price = parseFloat(selectedOption.getAttribute('data-price'));
            const stock = parseInt(selectedOption.getAttribute('data-stock'));
            const quantity = parseInt(quantityInput.value) || 0;
            
            // Jika mengubah produk yang sama, stok perlu disesuaikan
            const originalProductId = '<?php echo $product_id; ?>';
            const originalQuantity = parseInt('<?php echo $old_quantity; ?>');
            let adjustedStock = stock;
            
            if (selectedOption.value === originalProductId) {
                adjustedStock += originalQuantity;
            }
            
            if (quantity > adjustedStock) {
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
        }
    }
    
    productSelect.addEventListener('change', calculateTotal);
    quantityInput.addEventListener('input', calculateTotal);
    
    // Hitung total saat halaman dimuat
    document.addEventListener('DOMContentLoaded', calculateTotal);
</script>

<?php include '../../includes/footer.php'; ?>