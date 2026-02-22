<?php
declare(strict_types=1);
require_once __DIR__ . '/admin/inc/bootstrap.php';

if (admin_current_user() !== null) {
    header('Location: ' . admin_default_home_for_current_user());
    exit;
}

$error = '';
$success = '';
$activeTab = 'login';
$loginEmail = '';
$registerName = '';
$registerEmail = '';
$registerRequestId = (int) ($_SESSION['register_otp_request_id'] ?? 0);
$forgotEmail = '';
$forgotRequestId = (int) ($_SESSION['forgot_otp_request_id'] ?? 0);
$devOtpHint = '';
$otpCooldownFlow = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? 'login_submit');
    $activeTabInput = (string) ($_POST['active_tab'] ?? '');
    if (in_array($activeTabInput, ['login', 'register', 'forgot'], true)) {
        $activeTab = $activeTabInput;
    }

    $token = $_POST['csrf_token'] ?? '';
    if (!admin_verify_csrf(is_string($token) ? $token : null)) {
        $error = 'Token keamanan tidak valid.';
    } else {
        if ($action === 'login_submit') {
            $activeTab = 'login';
            $loginEmail = strtolower(trim((string) ($_POST['email'] ?? '')));
            $password = (string) ($_POST['password'] ?? '');
            $rememberMe = isset($_POST['remember_me']) && $_POST['remember_me'] === '1';

            if ($loginEmail === '' || $password === '') {
                $error = 'Email dan password wajib diisi.';
            } elseif (!admin_attempt_login($loginEmail, $password, $rememberMe)) {
                $error = 'Login gagal. Cek kredensial atau status akun.';
            } else {
                header('Location: ' . admin_default_home_for_current_user());
                exit;
            }
        } elseif ($action === 'register_request_otp') {
            $activeTab = 'register';
            $registerName = trim((string) ($_POST['name'] ?? ''));
            $registerEmail = strtolower(trim((string) ($_POST['register_email'] ?? '')));
            $password = (string) ($_POST['register_password'] ?? '');
            $confirmPassword = (string) ($_POST['register_confirm_password'] ?? '');

            if ($registerName === '' || $registerEmail === '' || $password === '' || $confirmPassword === '') {
                $error = 'Nama, email, password, dan konfirmasi password wajib diisi.';
            } elseif (!filter_var($registerEmail, FILTER_VALIDATE_EMAIL)) {
                $error = 'Format email tidak valid.';
            } elseif ($password !== $confirmPassword) {
                $error = 'Konfirmasi password tidak cocok.';
            } else {
                $passwordError = admin_validate_password($password);
                if ($passwordError !== null) {
                    $error = $passwordError;
                } else {
                    $otpPayload = [
                        'name' => $registerName,
                        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    ];
                    $otpResult = admin_create_otp_request($registerEmail, 'register_student', $otpPayload);
                    if (!($otpResult['ok'] ?? false)) {
                        $error = (string) ($otpResult['message'] ?? 'Gagal mengirim OTP.');
                    } else {
                        $registerRequestId = (int) ($otpResult['request_id'] ?? 0);
                        $_SESSION['register_otp_request_id'] = $registerRequestId;
                        $otpCooldownFlow = 'register';
                        $success = (string) ($otpResult['message'] ?? 'OTP terkirim.');
                        if (isset($otpResult['dev_otp']) && is_string($otpResult['dev_otp'])) {
                            $devOtpHint = 'Dev OTP (localhost): ' . $otpResult['dev_otp'];
                        }
                    }
                }
            }
        } elseif ($action === 'register_verify_otp') {
            $activeTab = 'register';
            $registerEmail = strtolower(trim((string) ($_POST['register_email_verify'] ?? '')));
            $registerRequestId = (int) ($_SESSION['register_otp_request_id'] ?? 0);
            $otpCode = trim((string) ($_POST['otp_code'] ?? ''));
            $rememberMe = isset($_POST['remember_me_after_register']) && $_POST['remember_me_after_register'] === '1';

            if ($registerRequestId <= 0) {
                $error = 'Sesi OTP tidak ditemukan. Silakan kirim OTP pendaftaran ulang.';
            } else {
            $verify = admin_verify_otp_request($registerRequestId, $registerEmail, 'register_student', $otpCode);
            if (!($verify['ok'] ?? false)) {
                $error = (string) ($verify['message'] ?? 'OTP tidak valid.');
            } else {
                $payload = is_array($verify['payload'] ?? null) ? $verify['payload'] : [];
                $name = trim((string) ($payload['name'] ?? 'Student'));
                $passwordHash = (string) ($payload['password_hash'] ?? '');
                $createUser = admin_register_student_with_password_hash($name, $registerEmail, $passwordHash);
                if (!($createUser['ok'] ?? false)) {
                    $error = (string) ($createUser['message'] ?? 'Gagal membuat akun student.');
                } else {
                    admin_consume_otp_request($registerRequestId);
                    unset($_SESSION['register_otp_request_id']);
                    if (admin_login_by_email($registerEmail, $rememberMe)) {
                        header('Location: ' . admin_default_home_for_current_user());
                        exit;
                    }
                    $success = 'Akun berhasil dibuat. Silakan login.';
                    $activeTab = 'login';
                }
            }
            }
        } elseif ($action === 'forgot_request_otp') {
            $activeTab = 'forgot';
            $forgotEmail = strtolower(trim((string) ($_POST['forgot_email'] ?? '')));
            if ($forgotEmail === '' || !filter_var($forgotEmail, FILTER_VALIDATE_EMAIL)) {
                $error = 'Masukkan email yang valid.';
            } else {
                $eligibleUser = admin_password_reset_eligible_user($forgotEmail);
                if ($eligibleUser) {
                    $otpResult = admin_create_otp_request($forgotEmail, 'password_reset', []);
                    if ($otpResult['ok'] ?? false) {
                        $forgotRequestId = (int) ($otpResult['request_id'] ?? 0);
                        $_SESSION['forgot_otp_request_id'] = $forgotRequestId;
                        $otpCooldownFlow = 'forgot';
                        if (isset($otpResult['dev_otp']) && is_string($otpResult['dev_otp'])) {
                            $devOtpHint = 'Dev OTP (localhost): ' . $otpResult['dev_otp'];
                        }
                    }
                }
                $success = 'Jika email terdaftar, OTP reset password sudah dikirim.';
            }
        } elseif ($action === 'forgot_verify_otp') {
            $activeTab = 'forgot';
            $forgotEmail = strtolower(trim((string) ($_POST['forgot_email_verify'] ?? '')));
            $forgotRequestId = (int) ($_SESSION['forgot_otp_request_id'] ?? 0);
            $otpCode = trim((string) ($_POST['otp_code'] ?? ''));
            $newPassword = (string) ($_POST['new_password'] ?? '');
            $confirmNewPassword = (string) ($_POST['confirm_new_password'] ?? '');

            if ($forgotRequestId <= 0) {
                $error = 'Sesi OTP reset tidak ditemukan. Silakan kirim OTP reset ulang.';
            } elseif ($newPassword !== $confirmNewPassword) {
                $error = 'Konfirmasi password baru tidak cocok.';
            } else {
                $verify = admin_verify_otp_request($forgotRequestId, $forgotEmail, 'password_reset', $otpCode);
                if (!($verify['ok'] ?? false)) {
                    $error = (string) ($verify['message'] ?? 'OTP tidak valid.');
                } else {
                    $reset = admin_reset_password_by_email($forgotEmail, $newPassword);
                    if (!($reset['ok'] ?? false)) {
                        $error = (string) ($reset['message'] ?? 'Gagal reset password.');
                    } else {
                        admin_consume_otp_request($forgotRequestId);
                        unset($_SESSION['forgot_otp_request_id']);
                        $success = 'Password berhasil diubah. Silakan login.';
                        $activeTab = 'login';
                    }
                }
            }
        } else {
            $error = 'Aksi tidak dikenali.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Login | InoSakti</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ["'Plus Jakarta Sans'", 'sans-serif'],
            display: ["'Space Grotesk'", 'sans-serif']
          },
          boxShadow: {
            panel: '0 24px 80px rgba(15, 23, 42, 0.16)'
          }
        }
      }
    };
  </script>
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-900">
  <div class="relative min-h-screen overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(14,165,233,0.32),transparent_45%),radial-gradient(circle_at_80%_10%,rgba(168,85,247,0.28),transparent_35%),radial-gradient(circle_at_50%_100%,rgba(34,197,94,0.22),transparent_45%)]"></div>
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-950 to-black"></div>

    <div class="relative min-h-screen grid lg:grid-cols-2 gap-6 p-4 sm:p-8">
      <section class="hidden lg:flex rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm p-12 text-white items-end">
        <div>
          <p class="inline-flex items-center rounded-full border border-white/20 px-4 py-1 text-xs font-semibold tracking-wide uppercase">InoSakti Unified Portal</p>
          <h1 class="mt-4 font-display text-5xl font-bold leading-tight">One Portal.<br>All Access.</h1>
          <p class="mt-5 text-slate-200 max-w-lg">Admin, instructor, employee, dan student masuk dari portal yang sama. Registrasi mandiri hanya untuk student dengan verifikasi OTP email.</p>
        </div>
      </section>

      <section class="flex items-center justify-center">
        <div class="w-full max-w-xl rounded-3xl bg-white p-6 sm:p-8 shadow-panel border border-slate-100">
          <div class="mb-6">
            <h2 class="text-3xl font-display font-bold text-slate-900">Portal Akun</h2>
            <p class="text-sm text-slate-500 mt-1">Login, daftar student, dan reset password dalam satu halaman aman.</p>
          </div>

          <?php if ($error !== ''): ?>
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
              <?= admin_e($error) ?>
            </div>
          <?php endif; ?>

          <?php if ($success !== ''): ?>
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
              <?= admin_e($success) ?>
            </div>
          <?php endif; ?>

          <?php if ($devOtpHint !== ''): ?>
            <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
              <?= admin_e($devOtpHint) ?>
            </div>
          <?php endif; ?>

          <div class="grid grid-cols-3 gap-2 rounded-2xl bg-slate-100 p-1.5">
            <button type="button" data-tab-btn="login" class="tab-btn rounded-xl px-3 py-2 text-sm font-semibold transition">Masuk</button>
            <button type="button" data-tab-btn="register" class="tab-btn rounded-xl px-3 py-2 text-sm font-semibold transition">Buat Akun</button>
            <button type="button" data-tab-btn="forgot" class="tab-btn rounded-xl px-3 py-2 text-sm font-semibold transition">Lupa Password</button>
          </div>

          <div class="mt-6">
            <section data-tab-panel="login" class="tab-panel space-y-4">
              <form method="post" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
                <input type="hidden" name="action" value="login_submit">
                <input type="hidden" name="active_tab" value="login">
                <input type="text" name="username" class="hidden" tabindex="-1" autocomplete="username">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                  <input type="email" name="email" value="<?= admin_e($loginEmail) ?>" class="w-full rounded-xl border-slate-300" placeholder="nama@inosakti.com" autocomplete="email" required>
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                  <input id="loginPassword" type="password" name="password" class="w-full rounded-xl border-slate-300" placeholder="Minimal 8 karakter" autocomplete="current-password" required>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <label class="inline-flex items-center gap-2 text-slate-600">
                    <input type="checkbox" name="remember_me" value="1" class="rounded border-slate-300">
                    Remember me (30 hari)
                  </label>
                  <label class="inline-flex items-center gap-2 text-slate-600">
                    <input type="checkbox" data-toggle-password="loginPassword" class="rounded border-slate-300">
                    Show password
                  </label>
                </div>
                <button type="submit" class="w-full rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 transition-colors">
                  Login Portal
                </button>
              </form>
            </section>

            <section data-tab-panel="register" class="tab-panel hidden space-y-5">
              <div class="rounded-xl border border-sky-100 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                Pendaftaran mandiri hanya untuk role <strong>student</strong>. Role employee/blogger/admin/teacher diatur dari admin panel.
              </div>
              <form method="post" class="space-y-4" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
                <input type="hidden" name="action" value="register_request_otp">
                <input type="hidden" name="active_tab" value="register">
                <input type="text" name="register_username" class="hidden" tabindex="-1" autocomplete="username">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                  <input type="text" name="name" value="<?= admin_e($registerName) ?>" class="w-full rounded-xl border-slate-300" placeholder="Nama Student" autocomplete="name" required>
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                  <input type="email" name="register_email" value="<?= admin_e($registerEmail) ?>" class="w-full rounded-xl border-slate-300" placeholder="email@student.com" autocomplete="email" required>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <input id="registerPassword" type="password" name="register_password" class="w-full rounded-xl border-slate-300" placeholder="Minimal 8 karakter" autocomplete="new-password" required>
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Konfirmasi Password</label>
                    <input id="registerConfirmPassword" type="password" name="register_confirm_password" class="w-full rounded-xl border-slate-300" placeholder="Ulangi password" autocomplete="new-password" required>
                  </div>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                  <input type="checkbox" data-toggle-password-group="registerPassword,registerConfirmPassword" class="rounded border-slate-300">
                  Show password
                </label>
                <button id="registerOtpBtn" type="submit" class="w-full rounded-xl bg-sky-700 hover:bg-sky-800 text-white font-bold py-2.5 transition-colors">
                  Kirim OTP Pendaftaran
                </button>
              </form>

              <form method="post" class="space-y-4 border-t border-slate-200 pt-5" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
                <input type="hidden" name="action" value="register_verify_otp">
                <input type="hidden" name="active_tab" value="register">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                  <input type="email" name="register_email_verify" value="<?= admin_e($registerEmail) ?>" class="w-full rounded-xl border-slate-300" autocomplete="email" required>
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1">Kode OTP</label>
                  <input type="text" name="otp_code" class="w-full rounded-xl border-slate-300 tracking-[0.35em] font-semibold text-center" maxlength="6" placeholder="000000" required>
                </div>
                <?php if ($registerRequestId > 0): ?>
                <div class="text-xs text-slate-500">OTP request tersimpan aman di sesi server.</div>
                <?php endif; ?>
                <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                  <input type="checkbox" name="remember_me_after_register" value="1" class="rounded border-slate-300">
                  Remember me setelah akun dibuat
                </label>
                <button type="submit" class="w-full rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-2.5 transition-colors">
                  Verifikasi OTP & Buat Akun
                </button>
              </form>
            </section>

            <section data-tab-panel="forgot" class="tab-panel hidden space-y-5">
              <form method="post" class="space-y-4" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
                <input type="hidden" name="action" value="forgot_request_otp">
                <input type="hidden" name="active_tab" value="forgot">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1">Email Akun</label>
                  <input type="email" name="forgot_email" value="<?= admin_e($forgotEmail) ?>" class="w-full rounded-xl border-slate-300" placeholder="nama@inosakti.com" autocomplete="email" required>
                </div>
                <button id="forgotOtpBtn" type="submit" class="w-full rounded-xl bg-indigo-700 hover:bg-indigo-800 text-white font-bold py-2.5 transition-colors">
                  Kirim OTP Reset Password
                </button>
              </form>

              <form method="post" class="space-y-4 border-t border-slate-200 pt-5" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
                <input type="hidden" name="action" value="forgot_verify_otp">
                <input type="hidden" name="active_tab" value="forgot">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                  <input type="email" name="forgot_email_verify" value="<?= admin_e($forgotEmail) ?>" class="w-full rounded-xl border-slate-300" autocomplete="email" required>
                </div>
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1">Kode OTP</label>
                  <input type="text" name="otp_code" class="w-full rounded-xl border-slate-300 tracking-[0.35em] font-semibold text-center" maxlength="6" placeholder="000000" required>
                </div>
                <?php if ($forgotRequestId > 0): ?>
                <div class="text-xs text-slate-500">OTP request tersimpan aman di sesi server.</div>
                <?php endif; ?>
                <div class="grid sm:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password Baru</label>
                    <input id="forgotPassword" type="password" name="new_password" class="w-full rounded-xl border-slate-300" placeholder="Minimal 8 karakter" required>
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Konfirmasi Password Baru</label>
                    <input id="forgotConfirmPassword" type="password" name="confirm_new_password" class="w-full rounded-xl border-slate-300" placeholder="Ulangi password baru" required>
                  </div>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                  <input type="checkbox" data-toggle-password-group="forgotPassword,forgotConfirmPassword" class="rounded border-slate-300">
                  Show password
                </label>
                <button type="submit" class="w-full rounded-xl bg-violet-700 hover:bg-violet-800 text-white font-bold py-2.5 transition-colors">
                  Verifikasi OTP & Simpan Password Baru
                </button>
              </form>
            </section>
          </div>
        </div>
      </section>
    </div>
  </div>

  <script>
    (function () {
      const activeTab = <?= json_encode($activeTab, JSON_UNESCAPED_SLASHES) ?>;
      const otpCooldownFlow = <?= json_encode($otpCooldownFlow, JSON_UNESCAPED_SLASHES) ?>;
      const tabs = ['login', 'register', 'forgot'];
      const btns = Array.from(document.querySelectorAll('[data-tab-btn]'));
      const panels = Array.from(document.querySelectorAll('[data-tab-panel]'));
      const otpButtons = {
        register: document.getElementById('registerOtpBtn'),
        forgot: document.getElementById('forgotOtpBtn')
      };
      const otpButtonLabels = {
        register: 'Kirim OTP Pendaftaran',
        forgot: 'Kirim OTP Reset Password'
      };

      function setTab(tab) {
        if (!tabs.includes(tab)) tab = 'login';
        btns.forEach((btn) => {
          const selected = btn.getAttribute('data-tab-btn') === tab;
          btn.classList.toggle('bg-white', selected);
          btn.classList.toggle('text-slate-900', selected);
          btn.classList.toggle('shadow-sm', selected);
          btn.classList.toggle('text-slate-500', !selected);
        });
        panels.forEach((panel) => {
          const selected = panel.getAttribute('data-tab-panel') === tab;
          panel.classList.toggle('hidden', !selected);
        });
      }

      btns.forEach((btn) => {
        btn.addEventListener('click', () => setTab(btn.getAttribute('data-tab-btn') || 'login'));
      });

      setTab(activeTab);

      function bindSingleToggle(checkboxEl, inputId) {
        const inputEl = document.getElementById(inputId);
        if (!inputEl) return;
        checkboxEl.addEventListener('change', () => {
          inputEl.type = checkboxEl.checked ? 'text' : 'password';
        });
      }

      document.querySelectorAll('[data-toggle-password]').forEach((el) => {
        const targetId = el.getAttribute('data-toggle-password');
        if (!targetId) return;
        bindSingleToggle(el, targetId);
      });

      document.querySelectorAll('[data-toggle-password-group]').forEach((el) => {
        const ids = (el.getAttribute('data-toggle-password-group') || '').split(',').map(v => v.trim()).filter(Boolean);
        el.addEventListener('change', () => {
          ids.forEach((id) => {
            const inputEl = document.getElementById(id);
            if (inputEl) {
              inputEl.type = el.checked ? 'text' : 'password';
            }
          });
        });
      });

      function cooldownStorageKey(flow) {
        return 'otpCooldown.' + flow;
      }

      function renderOtpCooldown(flow) {
        const btn = otpButtons[flow];
        if (!btn) return;
        const key = cooldownStorageKey(flow);
        const expiresAt = Number(sessionStorage.getItem(key) || '0');
        const now = Date.now();
        if (expiresAt <= now) {
          btn.disabled = false;
          btn.classList.remove('opacity-60', 'cursor-not-allowed');
          btn.textContent = otpButtonLabels[flow];
          sessionStorage.removeItem(key);
          return;
        }
        const remainSec = Math.max(1, Math.ceil((expiresAt - now) / 1000));
        btn.disabled = true;
        btn.classList.add('opacity-60', 'cursor-not-allowed');
        btn.textContent = 'Tunggu ' + remainSec + ' detik';
      }

      function startOtpCooldown(flow, seconds) {
        const key = cooldownStorageKey(flow);
        const expiresAt = Date.now() + (seconds * 1000);
        sessionStorage.setItem(key, String(expiresAt));
        renderOtpCooldown(flow);
      }

      ['register', 'forgot'].forEach((flow) => {
        renderOtpCooldown(flow);
      });

      if (otpCooldownFlow === 'register' || otpCooldownFlow === 'forgot') {
        startOtpCooldown(otpCooldownFlow, 60);
      }

      window.setInterval(() => {
        ['register', 'forgot'].forEach((flow) => {
          renderOtpCooldown(flow);
        });
      }, 500);
    })();
  </script>
</body>
</html>
