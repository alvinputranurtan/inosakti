<?php
declare(strict_types=1);
require_once __DIR__ . '/admin/inc/bootstrap.php';
if (admin_current_user() === null) {
    header('Location: ' . admin_url('/login'));
    exit;
}

$user = admin_current_user();
$roles = admin_user_roles();
$hasAdmin = admin_can_access_admin_panel();
$isInstructor = in_array('instructor', $roles, true);
$isEmployee = in_array('employee', $roles, true) || in_array('hr_admin', $roles, true);
$isStudent = in_array('student', $roles, true);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Akun | InoSakti</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: { fontFamily: { sans: ["'Plus Jakarta Sans'", 'sans-serif'] } } }
    };
  </script>
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900">
  <header class="bg-white border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-5 py-4 flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-widest text-slate-500 font-bold">InoSakti Account Portal</div>
        <h1 class="text-xl font-extrabold">Halo, <?= admin_e((string) ($user['name'] ?? 'User')) ?></h1>
      </div>
      <a href="<?= admin_e(admin_url('/logout')) ?>" class="text-sm font-semibold text-blue-800 hover:underline">Logout</a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-5 py-8">
    <div class="mb-5 text-sm text-slate-600">
      Role aktif: <span class="font-semibold"><?= admin_e(implode(', ', $roles ?: ['unassigned'])) ?></span>
    </div>

    <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-4">
      <?php if ($hasAdmin): ?>
      <a href="<?= admin_e(admin_url('/admin/')) ?>" class="bg-white border border-slate-200 rounded-2xl p-5 hover:border-blue-300 hover:shadow-sm transition">
        <div class="text-sm text-slate-500">Admin</div>
        <div class="text-lg font-extrabold mt-1">Admin Dashboard</div>
        <p class="text-sm text-slate-600 mt-2">Kelola CMS, kursus, order, user, dan analytics operasional.</p>
      </a>
      <?php endif; ?>

      <?php if ($isInstructor): ?>
      <a href="<?= admin_e(admin_url('/pages/learning')) ?>" class="bg-white border border-slate-200 rounded-2xl p-5 hover:border-blue-300 hover:shadow-sm transition">
        <div class="text-sm text-slate-500">Instructor</div>
        <div class="text-lg font-extrabold mt-1">Instructor Area</div>
        <p class="text-sm text-slate-600 mt-2">Akses area pembelajaran dan konten instruktur.</p>
      </a>
      <?php endif; ?>

      <?php if ($isEmployee): ?>
      <a href="<?= admin_e(admin_url('/pages/company/about')) ?>" class="bg-white border border-slate-200 rounded-2xl p-5 hover:border-blue-300 hover:shadow-sm transition">
        <div class="text-sm text-slate-500">Employee</div>
        <div class="text-lg font-extrabold mt-1">Employee Area</div>
        <p class="text-sm text-slate-600 mt-2">Akses informasi internal perusahaan dan operasional tim.</p>
      </a>
      <?php endif; ?>

      <?php if ($isStudent): ?>
      <a href="<?= admin_e(admin_url('/pages/learning')) ?>" class="bg-white border border-slate-200 rounded-2xl p-5 hover:border-blue-300 hover:shadow-sm transition">
        <div class="text-sm text-slate-500">Student</div>
        <div class="text-lg font-extrabold mt-1">Student Area</div>
        <p class="text-sm text-slate-600 mt-2">Masuk ke area pembelajaran, progres, dan materi kursus.</p>
      </a>
      <?php endif; ?>
    </div>

    <?php if (!$hasAdmin && !$isInstructor && !$isEmployee && !$isStudent): ?>
      <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        Akun Anda belum memiliki role operasional. Hubungi Super Admin untuk assignment role.
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
