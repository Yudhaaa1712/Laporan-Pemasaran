<?php
session_start();

// Cek jika sudah login, redirect ke dashboard
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: ../index.php');
    exit;
}

$error = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Kredensial dengan role
    if ($username === 'owner' && $password === 'saga1212') {
        // Set session untuk Admin
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'Administrator';
        $_SESSION['full_name'] = 'Owner';
        
        header('Location: ../index.php');
        exit;
        
    } elseif ($username === 'supervisor' && $password === 'saga1212') {
        // Set session untuk Supervisor
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'Supervisor';
        $_SESSION['full_name'] = 'Supervisor';
        
        header('Location: ../index.php');
        exit;
        
    } elseif ($username === 'staffgudang' && $password === 'saga1212') {
        // Set session untuk Staff Gudang
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'Staff Gudang';
        $_SESSION['full_name'] = 'Staff Gudang';
        
        header('Location: ../index.php');
        exit;
        
    } else {
        $error = 'Username atau password salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Pemasaran Freedom Cofe</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-amber-800">Freedom Cofe</h1>
            <p class="text-gray-600">Sistem Informasi Pemasaran Produk</p>
        </div>
        
        <?php if (!empty($error)) { ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php } ?>
        
        <form action="" method="POST">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-bold mb-2">Username</label>
                <input type="text" id="username" name="username" required
                       class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-amber-300">
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-amber-300">
            </div>
            
            <div class="flex justify-center">
                <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded-md w-full hover:bg-amber-700">
                    Login
                </button>
            </div>
        </form>
        
    </div>
</body>
</html>