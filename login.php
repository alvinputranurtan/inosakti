<?php
declare(strict_types=1);
require_once __DIR__ . '/admin/inc/bootstrap.php';

if (admin_current_user() !== null) {
    header('Location: ' . admin_default_home_for_current_user());
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_verify_csrf(is_string($token) ? $token : null)) {
        $error = 'Token keamanan tidak valid.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if ($email === '' || $password === '') {
            $error = 'Email dan password wajib diisi.';
        } elseif (!admin_attempt_login($email, $password)) {
            $error = 'Login gagal. Cek kredensial atau status akun.';
        } else {
            header('Location: ' . admin_default_home_for_current_user());
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | InoSakti</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
  <script>
    tailwind.config = {
      theme: { extend: { fontFamily: { sans: ["'Plus Jakarta Sans'", 'sans-serif'] } } }
    };
  </script>
</head>
<body class="min-h-screen bg-slate-100 font-sans">
  <div class="min-h-screen grid lg:grid-cols-2">
    <div class="hidden lg:flex bg-gradient-to-br from-blue-900 to-slate-900 text-white p-12 items-end">
      <div>
        <h1 class="text-4xl font-extrabold leading-tight">Unified Access</h1>
        <p class="mt-4 text-blue-100 max-w-md">Satu halaman login untuk Admin, Instructor, Employee, dan Student.</p>
      </div>
    </div>
    <div class="flex items-center justify-center p-6">
      <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-slate-200 p-7">
        <h2 class="text-2xl font-extrabold text-slate-800">Masuk InoSakti</h2>
        <p class="text-sm text-slate-500 mt-1">Gunakan akun aktif yang tersimpan di tabel <code>users</code>.</p>

        <?php if ($error !== ''): ?>
          <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
            <?= admin_e($error) ?>
          </div>
        <?php endif; ?>

        <form method="post" class="mt-6 space-y-4">
          <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
            <input type="email" name="email" class="w-full rounded-xl border-slate-300" placeholder="nama@inosakti.com" required>
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
            <input type="password" name="password" class="w-full rounded-xl border-slate-300" placeholder="********" required>
          </div>
          <button type="submit" class="w-full rounded-xl bg-blue-800 hover:bg-blue-900 text-white font-bold py-2.5 transition-colors">
            Login
          </button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
