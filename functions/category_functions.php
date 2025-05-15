<?php
// Fungsi untuk mendapatkan semua kategori
function getAllCategories($conn) {
    $sql = "SELECT * FROM categories ORDER BY name ASC";
    $result = mysqli_query($conn, $sql);
    $categories = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    }
    
    return $categories;
}

// Fungsi untuk mendapatkan kategori berdasarkan ID
function getCategoryById($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $sql = "SELECT * FROM categories WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Fungsi untuk menambah kategori baru
function addCategory($conn, $name, $description = '') {
    // Escape input untuk mencegah SQL injection
    $name = mysqli_real_escape_string($conn, trim($name));
    $description = mysqli_real_escape_string($conn, trim($description));
    
    // Cek apakah kategori dengan nama yang sama sudah ada
    $checkSql = "SELECT id FROM categories WHERE name = '$name'";
    $checkResult = mysqli_query($conn, $checkSql);
    
    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        return false; // Kategori sudah ada
    }
    
    $sql = "INSERT INTO categories (name, description, created_at) VALUES ('$name', '$description', NOW())";
    
    if (mysqli_query($conn, $sql)) {
        return true;
    }
    
    return false;
}

// Fungsi untuk mengupdate kategori
function updateCategory($conn, $id, $name, $description = '') {
    $id = mysqli_real_escape_string($conn, $id);
    $name = mysqli_real_escape_string($conn, trim($name));
    $description = mysqli_real_escape_string($conn, trim($description));
    
    // Cek apakah kategori dengan nama yang sama sudah ada (kecuali kategori yang sedang diupdate)
    $checkSql = "SELECT id FROM categories WHERE name = '$name' AND id != '$id'";
    $checkResult = mysqli_query($conn, $checkSql);
    
    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        return false; // Kategori dengan nama sama sudah ada
    }
    
    $sql = "UPDATE categories SET name = '$name', description = '$description', updated_at = NOW() WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        return true;
    }
    
    return false;
}

// Fungsi untuk menghapus kategori
function deleteCategory($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    
    // Cek apakah ada produk yang menggunakan kategori ini
    $checkSql = "SELECT COUNT(*) as count FROM products WHERE category_id = '$id'";
    $checkResult = mysqli_query($conn, $checkSql);
    
    if ($checkResult) {
        $row = mysqli_fetch_assoc($checkResult);
        if ($row['count'] > 0) {
            return false; // Tidak bisa hapus karena masih ada produk yang menggunakan kategori ini
        }
    }
    
    $sql = "DELETE FROM categories WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        return true;
    }
    
    return false;
}

// Fungsi untuk mendapatkan jumlah produk dalam kategori
function getProductCountByCategory($conn) {
    $sql = "SELECT c.id, c.name, c.description, COUNT(p.id) as product_count 
            FROM categories c 
            LEFT JOIN products p ON c.id = p.category_id 
            GROUP BY c.id, c.name, c.description
            ORDER BY c.name ASC";
    
    $result = mysqli_query($conn, $sql);
    $categories = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    }
    
    return $categories;
}

// Fungsi untuk mendapatkan kategori dengan paginasi
function getCategoriesWithPagination($conn, $offset = 0, $limit = 10) {
    $offset = (int)$offset;
    $limit = (int)$limit;
    
    $sql = "SELECT c.*, COUNT(p.id) as product_count 
            FROM categories c 
            LEFT JOIN products p ON c.id = p.category_id 
            GROUP BY c.id 
            ORDER BY c.name ASC 
            LIMIT $offset, $limit";
    
    $result = mysqli_query($conn, $sql);
    $categories = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    }
    
    return $categories;
}

// Fungsi untuk mendapatkan total kategori
function getTotalCategories($conn) {
    $sql = "SELECT COUNT(*) as total FROM categories";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    
    return 0;
}

// Fungsi untuk mencari kategori berdasarkan nama
function searchCategories($conn, $searchTerm) {
    $searchTerm = mysqli_real_escape_string($conn, trim($searchTerm));
    
    $sql = "SELECT c.*, COUNT(p.id) as product_count 
            FROM categories c 
            LEFT JOIN products p ON c.id = p.category_id 
            WHERE c.name LIKE '%$searchTerm%' OR c.description LIKE '%$searchTerm%'
            GROUP BY c.id 
            ORDER BY c.name ASC";
    
    $result = mysqli_query($conn, $sql);
    $categories = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    }
    
    return $categories;
}

// Fungsi untuk cek apakah nama kategori sudah ada
function categoryNameExists($conn, $name, $excludeId = null) {
    $name = mysqli_real_escape_string($conn, trim($name));
    $sql = "SELECT id FROM categories WHERE name = '$name'";
    
    if ($excludeId) {
        $excludeId = mysqli_real_escape_string($conn, $excludeId);
        $sql .= " AND id != '$excludeId'";
    }
    
    $result = mysqli_query($conn, $sql);
    return ($result && mysqli_num_rows($result) > 0);
}

// Fungsi untuk mendapatkan statistik kategori
function getCategoryStats($conn) {
    $stats = [];
    
    // Total kategori
    $sql = "SELECT COUNT(*) as total_categories FROM categories";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $stats['total_categories'] = $row['total_categories'];
    }
    
    // Kategori dengan produk terbanyak
    $sql = "SELECT c.name, COUNT(p.id) as product_count 
            FROM categories c 
            LEFT JOIN products p ON c.id = p.category_id 
            GROUP BY c.id, c.name 
            ORDER BY product_count DESC 
            LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $stats['most_products_category'] = $row;
    }
    
    // Kategori tanpa produk
    $sql = "SELECT COUNT(*) as empty_categories 
            FROM categories c 
            LEFT JOIN products p ON c.id = p.category_id 
            WHERE p.id IS NULL";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $stats['empty_categories'] = $row['empty_categories'];
    }
    
    return $stats;
}
?>