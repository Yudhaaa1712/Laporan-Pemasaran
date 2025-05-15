<?php
// Fungsi untuk mendapatkan semua produk
function getAllProducts($conn) {
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            INNER JOIN categories c ON p.category_id = c.id 
            ORDER BY p.id DESC";
    $result = mysqli_query($conn, $sql);
    $products = [];
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Fungsi untuk mendapatkan produk berdasarkan ID
function getProductById($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $sql = "SELECT * FROM products WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Fungsi untuk menambah produk baru
function addProduct($conn, $name, $category_id, $price, $stock, $description, $image = null) {
    $name = mysqli_real_escape_string($conn, $name);
    $category_id = mysqli_real_escape_string($conn, $category_id);
    $price = mysqli_real_escape_string($conn, $price);
    $stock = mysqli_real_escape_string($conn, $stock);
    $description = mysqli_real_escape_string($conn, $description);
    $image = $image ? mysqli_real_escape_string($conn, $image) : null;
    
    $sql = "INSERT INTO products (name, category_id, price, stock, description, image) 
            VALUES ('$name', '$category_id', '$price', '$stock', '$description', '$image')";
    
    if (mysqli_query($conn, $sql)) {
        return true;
    }
    
    return false;
}

// Fungsi untuk mengupdate produk
function updateProduct($conn, $id, $name, $category_id, $price, $stock, $description, $image = null) {
    $id = mysqli_real_escape_string($conn, $id);
    $name = mysqli_real_escape_string($conn, $name);
    $category_id = mysqli_real_escape_string($conn, $category_id);
    $price = mysqli_real_escape_string($conn, $price);
    $stock = mysqli_real_escape_string($conn, $stock);
    $description = mysqli_real_escape_string($conn, $description);
    
    $image_query = "";
    if ($image) {
        $image = mysqli_real_escape_string($conn, $image);
        $image_query = ", image = '$image'";
    }
    
    $sql = "UPDATE products 
            SET name = '$name', category_id = '$category_id', price = '$price', 
                stock = '$stock', description = '$description'$image_query 
            WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        return true;
    }
    
    return false;
}

// Fungsi untuk menghapus produk
function deleteProduct($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $sql = "DELETE FROM products WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        return true;
    }
    
    return false;
}

// Fungsi untuk mencari produk
function searchProducts($conn, $keyword) {
    $keyword = mysqli_real_escape_string($conn, $keyword);
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            INNER JOIN categories c ON p.category_id = c.id 
            WHERE p.name LIKE '%$keyword%' 
            OR p.description LIKE '%$keyword%' 
            OR c.name LIKE '%$keyword%'";
    
    $result = mysqli_query($conn, $sql);
    $products = [];
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Fungsi untuk mendapatkan total produk
function getTotalProducts($conn) {
    $sql = "SELECT COUNT(*) as total FROM products";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

// Fungsi untuk mendapatkan produk terlaris
function getBestSellingProduct($conn) {
    $sql = "SELECT p.name, SUM(s.quantity) as total_sold 
            FROM products p 
            INNER JOIN sales s ON p.id = s.product_id 
            GROUP BY p.id 
            ORDER BY total_sold DESC 
            LIMIT 1";
    
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row;
    }
    
    return null;
}

// Fungsi untuk mendapatkan total penjualan
function getTotalSales($conn) {
    $sql = "SELECT SUM(total_price) as total FROM sales";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}
?>