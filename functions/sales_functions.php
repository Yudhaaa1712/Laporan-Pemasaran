<?php
// Fungsi untuk mendapatkan semua data penjualan
function getAllSales($conn) {
    $sql = "SELECT s.*, p.name as product_name 
            FROM sales s 
            INNER JOIN products p ON s.product_id = p.id 
            ORDER BY s.sale_date DESC";
    $result = mysqli_query($conn, $sql);
    $sales = [];
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $sales[] = $row;
        }
    }
    
    return $sales;
}

// Fungsi untuk mendapatkan data penjualan berdasarkan ID
function getSaleById($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $sql = "SELECT * FROM sales WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Fungsi untuk mendapatkan penjualan per hari
function getSalesByDate($conn, $start_date, $end_date) {
    $start_date = mysqli_real_escape_string($conn, $start_date);
    $end_date = mysqli_real_escape_string($conn, $end_date);
    
    $sql = "SELECT sale_date, SUM(quantity) as total_quantity, SUM(total_price) as total_sales
            FROM sales
            WHERE sale_date BETWEEN '$start_date' AND '$end_date'
            GROUP BY sale_date
            ORDER BY sale_date";
    
    $result = mysqli_query($conn, $sql);
    $sales = [];
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $sales[] = $row;
        }
    }
    
    return $sales;
}

// Fungsi untuk mendapatkan penjualan per produk
function getSalesByProduct($conn, $start_date, $end_date) {
    $start_date = mysqli_real_escape_string($conn, $start_date);
    $end_date = mysqli_real_escape_string($conn, $end_date);
    
    $sql = "SELECT p.name as product_name, SUM(s.quantity) as total_quantity, SUM(s.total_price) as total_sales
            FROM sales s
            JOIN products p ON s.product_id = p.id
            WHERE s.sale_date BETWEEN '$start_date' AND '$end_date'
            GROUP BY s.product_id
            ORDER BY total_sales DESC";
    
    $result = mysqli_query($conn, $sql);
    $sales = [];
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $sales[] = $row;
        }
    }
    
    return $sales;
}

// Fungsi untuk menambah penjualan
function addSale($conn, $product_id, $quantity, $sale_date) {
    $product_id = mysqli_real_escape_string($conn, $product_id);
    $quantity = mysqli_real_escape_string($conn, $quantity);
    $sale_date = mysqli_real_escape_string($conn, $sale_date);
    
    // Mendapatkan harga produk
    $product_query = "SELECT price, stock FROM products WHERE id = '$product_id'";
    $product_result = mysqli_query($conn, $product_query);
    $product = mysqli_fetch_assoc($product_result);
    
    if (!$product) {
        return false;
    }
    
    $price = $product['price'];
    $current_stock = $product['stock'];
    
    // Cek stok
    if ($current_stock < $quantity) {
        return false;
    }
    
    $total_price = $price * $quantity;
    
    // Begin transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert penjualan
        $sql = "INSERT INTO sales (product_id, quantity, total_price, sale_date) 
                VALUES ('$product_id', '$quantity', '$total_price', '$sale_date')";
        
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error inserting sale");
        }
        
        // Update stok
        $new_stock = $current_stock - $quantity;
        $update_stock = "UPDATE products SET stock = '$new_stock' WHERE id = '$product_id'";
        
        if (!mysqli_query($conn, $update_stock)) {
            throw new Exception("Error updating stock");
        }
        
        // Commit transaction
        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        return false;
    }
}

// Fungsi untuk mengupdate penjualan
function updateSale($conn, $id, $product_id, $quantity, $sale_date) {
    $id = mysqli_real_escape_string($conn, $id);
    $product_id = mysqli_real_escape_string($conn, $product_id);
    $quantity = mysqli_real_escape_string($conn, $quantity);
    $sale_date = mysqli_real_escape_string($conn, $sale_date);
    
    // Dapatkan data penjualan lama
    $old_sale_query = "SELECT product_id, quantity FROM sales WHERE id = '$id'";
    $old_sale_result = mysqli_query($conn, $old_sale_query);
    $old_sale = mysqli_fetch_assoc($old_sale_result);
    
    if (!$old_sale) {
        return false;
    }
    
    $old_product_id = $old_sale['product_id'];
    $old_quantity = $old_sale['quantity'];
    
    // Mendapatkan harga produk baru
    $product_query = "SELECT price, stock FROM products WHERE id = '$product_id'";
    $product_result = mysqli_query($conn, $product_query);
    $product = mysqli_fetch_assoc($product_result);
    
    if (!$product) {
        return false;
    }
    
    $price = $product['price'];
    $current_stock = $product['stock'];
    
    // Begin transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Jika produk berubah
        if ($old_product_id != $product_id) {
            // Kembalikan stok produk lama
            $old_product_query = "SELECT stock FROM products WHERE id = '$old_product_id'";
            $old_product_result = mysqli_query($conn, $old_product_query);
            $old_product = mysqli_fetch_assoc($old_product_result);
            
            if (!$old_product) {
                throw new Exception("Old product not found");
            }
            
            $old_stock = $old_product['stock'];
            $updated_old_stock = $old_stock + $old_quantity;
            
            $update_old_stock = "UPDATE products SET stock = '$updated_old_stock' WHERE id = '$old_product_id'";
            if (!mysqli_query($conn, $update_old_stock)) {
                throw new Exception("Error updating old product stock");
            }
            
            // Cek stok produk baru
            if ($current_stock < $quantity) {
                throw new Exception("Insufficient stock for new product");
            }
            
            // Kurangi stok produk baru
            $updated_new_stock = $current_stock - $quantity;
            $update_new_stock = "UPDATE products SET stock = '$updated_new_stock' WHERE id = '$product_id'";
            
            if (!mysqli_query($conn, $update_new_stock)) {
                throw new Exception("Error updating new product stock");
            }
        } else {
            // Produk sama, update stok
            $diff = $quantity - $old_quantity;
            
            // Cek stok cukup tidak
            if ($current_stock < $diff) {
                throw new Exception("Insufficient stock");
            }
            
            $updated_stock = $current_stock - $diff;
            $update_stock = "UPDATE products SET stock = '$updated_stock' WHERE id = '$product_id'";
            
            if (!mysqli_query($conn, $update_stock)) {
                throw new Exception("Error updating product stock");
            }
        }
        
        // Hitung total harga baru
        $total_price = $price * $quantity;
        
        // Update data penjualan
        $sql = "UPDATE sales SET product_id = '$product_id', quantity = '$quantity', 
                total_price = '$total_price', sale_date = '$sale_date' WHERE id = '$id'";
        
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error updating sale");
        }
        
        // Commit transaction
        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        return false;
    }
}

// Fungsi untuk menghapus penjualan
function deleteSale($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    
    // Dapatkan data penjualan sebelum dihapus
    $sale_query = "SELECT product_id, quantity FROM sales WHERE id = '$id'";
    $sale_result = mysqli_query($conn, $sale_query);
    
    if (mysqli_num_rows($sale_result) == 0) {
        return false;
    }
    
    $sale = mysqli_fetch_assoc($sale_result);
    $product_id = $sale['product_id'];
    $quantity = $sale['quantity'];
    
    // Begin transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Kembalikan stok produk
        $product_query = "SELECT stock FROM products WHERE id = '$product_id'";
        $product_result = mysqli_query($conn, $product_query);
        $product = mysqli_fetch_assoc($product_result);
        
        if (!$product) {
            throw new Exception("Product not found");
        }
        
        $current_stock = $product['stock'];
        $updated_stock = $current_stock + $quantity;
        
        $update_stock = "UPDATE products SET stock = '$updated_stock' WHERE id = '$product_id'";
        if (!mysqli_query($conn, $update_stock)) {
            throw new Exception("Error updating product stock");
        }
        
        // Hapus data penjualan
        $sql = "DELETE FROM sales WHERE id = '$id'";
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error deleting sale");
        }
        
        // Commit transaction
        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        return false;
    }
}
?>