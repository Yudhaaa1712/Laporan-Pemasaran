<?php
// Memulai sesi PHP
session_start();

// Cek jika pengguna sudah login, arahkan ke halaman dashboard
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: ../index.php');
    exit;
}

// Inisialisasi variabel error dan sukses
$error = '';
$success_message = '';
$show_reset_form = false; // Flag untuk menampilkan form reset password

// --- DATA PENGGUNA (SIMULASI DATABASE) ---
// Dalam aplikasi nyata, ini harus diambil dari database yang aman
// Password HARUS di-hash menggunakan password_hash() untuk keamanan!
$users = [
    'admin' => [
        'password' => 'saga1212', // Contoh password asli (tidak aman untuk produksi)
        'full_name' => 'Admin'
    ],
    'staffgudang' => [
        'password' => 'saga1212', // Contoh password asli (tidak aman untuk produksi)
        'full_name' => 'Staff Gudang'
    ],
];
// --- AKHIR DATA PENGGUNA ---


// Menangani permintaan POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Logika untuk LUPA PASSWORD
        if ($_POST['action'] === 'forgot_password_username') {
            $username_forgot = $_POST['username_forgot'] ?? '';

            if (array_key_exists($username_forgot, $users)) {
                // Username ditemukan, tampilkan form untuk set password baru
                $_SESSION['reset_username'] = $username_forgot; // Simpan username di session untuk proses reset
                $show_reset_form = true;
                $success_message = "Username ditemukan! Silakan atur password baru Anda.";
            } else {
                $error = "Username tidak ditemukan.";
            }
        } 
        // Logika untuk RESET PASSWORD BARU
        elseif ($_POST['action'] === 'reset_password') {
            $reset_username = $_SESSION['reset_username'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($reset_username)) {
                $error = "Terjadi kesalahan. Silakan coba lagi dari awal (lupa password).";
            } elseif (empty($new_password) || empty($confirm_password)) {
                $error = "Password baru dan konfirmasi password tidak boleh kosong.";
            } elseif ($new_password !== $confirm_password) {
                $error = "Password baru dan konfirmasi password tidak cocok.";
            } else {
                // Dalam aplikasi nyata: UPDATE password di database
                // Contoh: $users[$reset_username]['password'] = password_hash($new_password, PASSWORD_DEFAULT);
                // Untuk demo ini, kita tidak mengubah array $users secara persisten.
                
                // Hancurkan session reset setelah berhasil
                unset($_SESSION['reset_username']);
                $success_message = "Password Anda berhasil diubah! Silakan login dengan password baru.";
                // Setelah berhasil reset, kembalikan ke form login
                $show_reset_form = false; 
            }
        }
    } else {
        // Logika untuk LOGIN
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (array_key_exists($username, $users) && $users[$username]['password'] === $password) { // Di dunia nyata: password_verify($password, $users[$username]['password_hash'])
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = ($username === 'admin') ? 'Administrator' : 'Staff Gudang';
            $_SESSION['full_name'] = $users[$username]['full_name'];
            
            header('Location: ../index.php');
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    }
}

// Menangani permintaan GET (untuk menampilkan form lupa password awal)
if (isset($_GET['action']) && $_GET['action'] === 'forgot_password') {
    $show_reset_form = true; // Langsung tampilkan form lupa password
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Pemasaran Freedom Coffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-amber-800">Freedom Coffee</h1>
            <p class="text-gray-600">Sistem Informasi Pemasaran Produk</p>
        </div>
        
        <?php 
        // Menampilkan pesan error jika ada
        if (!empty($error)) { 
        ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php 
        } 
        ?>

        <?php 
        // Menampilkan pesan sukses jika ada
        if (!empty($success_message)) { 
        ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success_message; ?>
            </div>
        <?php 
        } 
        ?>
        
        <form id="loginForm" action="" method="POST" class="<?php echo ($show_reset_form && !isset($_SESSION['reset_username'])) ? 'hidden' : ''; ?>">
            <?php if (!isset($_SESSION['reset_username'])) { ?>
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
                
                <div class="mt-4 text-center">
                    <a href="#" id="showForgotPassword" class="text-sm text-amber-600 hover:underline">Lupa Password?</a>
                </div>
            <?php } ?>
        </form>

        <form id="forgotPasswordUsernameForm" action="" method="POST" class="<?php echo ($show_reset_form && !isset($_SESSION['reset_username'])) ? '' : 'hidden'; ?>">
            <input type="hidden" name="action" value="forgot_password_username">
            <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">Lupa Password</h2>
            <p class="text-gray-600 text-center mb-4">Masukkan Username Anda untuk mereset password.</p>
            <div class="mb-4">
                <label for="username_forgot" class="block text-gray-700 font-bold mb-2">Username</label>
                <input type="text" id="username_forgot" name="username_forgot" required
                       class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-amber-300">
            </div>
            
            <div class="flex justify-center">
                <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded-md w-full hover:bg-amber-700">
                    Cari Akun
                </button>
            </div>
            <div class="mt-4 text-center">
                <a href="#" id="showLoginFromForgotUsername" class="text-sm text-amber-600 hover:underline">Kembali ke Login</a>
            </div>
        </form>

        <form id="resetPasswordForm" action="" method="POST" class="<?php echo (isset($_SESSION['reset_username']) && $show_reset_form) ? '' : 'hidden'; ?>">
            <input type="hidden" name="action" value="reset_password">
            <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">Atur Password Baru</h2>
            <p class="text-gray-600 text-center mb-4">Untuk Username: <strong class="text-amber-700"><?php echo htmlspecialchars($_SESSION['reset_username'] ?? ''); ?></strong></p>
            <div class="mb-4">
                <label for="new_password" class="block text-gray-700 font-bold mb-2">Password Baru</label>
                <input type="password" id="new_password" name="new_password" required
                       class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-amber-300">
            </div>
            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 font-bold mb-2">Konfirmasi Password Baru</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-amber-300">
            </div>
            
            <div class="flex justify-center">
                <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded-md w-full hover:bg-amber-700">
                    Ubah Password
                </button>
            </div>
            <div class="mt-4 text-center">
                <a href="#" id="showLoginFromReset" class="text-sm text-amber-600 hover:underline">Kembali ke Login</a>
            </div>
        </form>
    </div>

    <script>
        // Mengatur tampilan form login, lupa password (username), dan reset password
        const loginForm = document.getElementById('loginForm');
        const forgotPasswordUsernameForm = document.getElementById('forgotPasswordUsernameForm');
        const resetPasswordForm = document.getElementById('resetPasswordForm');
        const showForgotPassword = document.getElementById('showForgotPassword');
        const showLoginFromForgotUsername = document.getElementById('showLoginFromForgotUsername');
        const showLoginFromReset = document.getElementById('showLoginFromReset');

        // Fungsi untuk menampilkan form tertentu
        function showForm(formToShow) {
            loginForm.classList.add('hidden');
            forgotPasswordUsernameForm.classList.add('hidden');
            resetPasswordForm.classList.add('hidden');
            formToShow.classList.remove('hidden');
        }

        // Event listener untuk tombol "Lupa Password?" di form login
        if (showForgotPassword) { // Pastikan elemen ada
            showForgotPassword.addEventListener('click', function(e) {
                e.preventDefault();
                showForm(forgotPasswordUsernameForm);
            });
        }

        // Event listener untuk tombol "Kembali ke Login" di form lupa password (username)
        if (showLoginFromForgotUsername) { // Pastikan elemen ada
            showLoginFromForgotUsername.addEventListener('click', function(e) {
                e.preventDefault();
                showForm(loginForm);
            });
        }

        // Event listener untuk tombol "Kembali ke Login" di form reset password
        if (showLoginFromReset) { // Pastikan elemen ada
            showLoginFromReset.addEventListener('click', function(e) {
                e.preventDefault();
                showForm(loginForm);
            });
        }

        // Logika untuk menampilkan form yang benar saat halaman dimuat
        // Berdasarkan kondisi PHP
        <?php if ($show_reset_form && !isset($_SESSION['reset_username'])) { ?>
            showForm(forgotPasswordUsernameForm);
        <?php } elseif (isset($_SESSION['reset_username']) && $show_reset_form) { ?>
            showForm(resetPasswordForm);
        <?php } else { ?>
            showForm(loginForm);
        <?php } ?>
    </script>
</body>
</html>