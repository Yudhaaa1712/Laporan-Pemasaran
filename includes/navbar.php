<?php
// includes/navbar.php - Versi Fixed

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah session sudah ada dan valid
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect ke login jika belum login
    header('Location: /muha/auth/login.php');
    exit;
}

// Helper functions untuk menghindari undefined error
function getUserName() {
    return isset($_SESSION['full_name']) && !empty($_SESSION['full_name']) ? $_SESSION['full_name'] : 'User';
}

function getUserRole() {
    return isset($_SESSION['role']) && !empty($_SESSION['role']) ? $_SESSION['role'] : 'Guest';
}

function hasPageAccess($required_role = 'any') {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        return false;
    }
    
    if ($required_role === 'any') {
        return true;
    }
    
    $user_role = getUserRole();
    
    // Administrator bisa akses semua
    if ($user_role === 'Administrator') {
        return true;
    }
    
    // Supervisor bisa akses hampir semua kecuali admin-only
    if ($user_role === 'Supervisor' && $required_role !== 'Administrator') {
        return true;
    }
    
    // Staff Gudang hanya bisa akses yang diizinkan
    if ($user_role === 'Staff Gudang' && $required_role === 'Staff Gudang') {
        return true;
    }
    
    return false;
}
?>

<nav class="bg-amber-800 text-white p-4">
    <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
        <a href="/muha/index.php" class="text-xl font-bold mb-2 md:mb-0">Freedom Coffee</a>
        <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-4">
            <!-- Navigation Links dengan Role-Based Access -->
            <div class="flex items-center space-x-4">
                <a href="/muha/index.php" class="hover:underline">Beranda</a>
                <a href="/muha/pages/product/index.php" class="hover:underline">Produk</a>
                <a href="/muha/pages/category/index.php" class="hover:underline">Kategori</a>
                <a href="/muha/pages/sales/index.php" class="hover:underline">Penjualan</a>
                <a href="/muha/pages/aboutus/index.pphp" class="hover:underline">Tentang Kami</a>
            
                
                <?php if (hasPageAccess('Supervisor')) { ?>
                    <!-- Menu ini hanya untuk Admin dan Supervisor -->
                    <a href="/muha/pages/reports/index.php" class="hover:underline">Laporan</a>
                <?php } ?>
                
                
            </div>
            
            <div class="border-l border-amber-400 h-6 mx-2 hidden md:block"></div>
            
            <!-- User Info dan Logout -->
            <div class="flex items-center space-x-3">
                <!-- Role Badge -->
                <span class="px-2 py-1 text-xs rounded-full font-medium
                    <?php 
                    $role = getUserRole();
                    echo $role === 'Administrator' ? 'bg-red-600' : 
                         ($role === 'Supervisor' ? 'bg-blue-600' : 'bg-green-600');
                    ?>">
                    <?php echo htmlspecialchars($role); ?>
                </span>
                
                <!-- Welcome Text dengan User Info -->
                <span class="text-sm">
                    Halo, <span class="font-semibold"><?php echo htmlspecialchars(getUserName()); ?></span>
                </span>
                
                <!-- Logout button -->
                <a href="/MUHA/pages/logout.php" 
                   class="bg-amber-700 px-3 py-1 rounded hover:bg-amber-900 transition-colors"
                   onclick="return confirm('Apakah Anda yakin ingin logout?')">
                    Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<header class="bg-amber-600 text-white py-8">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl font-bold mb-2">Sistem Informasi Pemasaran Produk</h1>
        <p class="text-xl">Freedom Coffee</p>
    </div>
</header>