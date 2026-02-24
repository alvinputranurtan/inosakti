<?php
declare(strict_types=1);

function admin_otp_table_exists(): bool
{
    return admin_table_exists('auth_otp_codes');
}

function admin_otp_log_table_exists(): bool
{
    return admin_table_exists('auth_otp_logs');
}

function admin_log_otp_event(
    string $email,
    string $purpose,
    string $event,
    string $status,
    string $message,
    ?int $requestId = null,
    array $meta = []
): void {
    if (!admin_otp_log_table_exists()) {
        return;
    }

    $email = strtolower(trim($email));
    if ($email === '') {
        $email = '-';
    }
    $purpose = trim($purpose) !== '' ? trim($purpose) : '-';
    $event = trim($event) !== '' ? trim($event) : 'unknown';
    $status = trim($status) !== '' ? trim($status) : 'info';
    $message = trim($message);

    $ipAddress = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    if ($ipAddress === '') {
        $ipAddress = null;
    }
    $userAgent = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
    if ($userAgent === '') {
        $userAgent = null;
    }

    $metaJson = null;
    if ($meta) {
        $encoded = json_encode($meta, JSON_UNESCAPED_SLASHES);
        if (is_string($encoded)) {
            $metaJson = $encoded;
        }
    }

    $sql = "INSERT INTO auth_otp_logs
            (request_id, email, purpose, event, status, message, ip_address, user_agent, meta_json, created_at)
            VALUES (NULLIF(?, 0), ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = admin_db()->prepare($sql);
    if (!$stmt) {
        return;
    }
    $requestIdInt = $requestId ?? 0;
    $stmt->bind_param(
        'issssssss',
        $requestIdInt,
        $email,
        $purpose,
        $event,
        $status,
        $message,
        $ipAddress,
        $userAgent,
        $metaJson
    );
    $stmt->execute();
    $stmt->close();
}

function admin_env_bool(string $key, bool $default = false): bool
{
    $raw = inosakti_env_value($key);
    if ($raw === null) {
        return $default;
    }
    $val = strtolower(trim($raw));
    if ($val === '') {
        return $default;
    }
    return in_array($val, ['1', 'true', 'yes', 'on'], true);
}

function admin_mailer_autoload(): void
{
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $autoload = dirname(__DIR__, 2) . '/vendor/autoload.php';
    if (is_file($autoload) && is_readable($autoload)) {
        require_once $autoload;
    }
    $loaded = true;
}

function admin_smtp_enabled(): bool
{
    return admin_env_bool('SMTP_ENABLED', false);
}

function admin_send_email_via_smtp(string $toEmail, string $subject, string $htmlBody): bool
{
    admin_mailer_autoload();
    if (!class_exists('\\PHPMailer\\PHPMailer\\PHPMailer')) {
        return false;
    }

    $host = trim((string) inosakti_env_value('SMTP_HOST', ''));
    $port = (int) inosakti_env_value('SMTP_PORT', '587');
    $username = trim((string) inosakti_env_value('SMTP_USERNAME', ''));
    $password = (string) inosakti_env_value('SMTP_PASSWORD', '');
    $fromEmail = trim((string) inosakti_env_value('SMTP_FROM_EMAIL', $username));
    $fromName = trim((string) inosakti_env_value('SMTP_FROM_NAME', 'InoSakti'));
    $secureMode = strtolower(trim((string) inosakti_env_value('SMTP_SECURE', 'tls')));
    $smtpAuth = admin_env_bool('SMTP_AUTH', true);
    $allowSelfSigned = admin_env_bool('SMTP_ALLOW_SELF_SIGNED', false);

    if ($host === '' || $fromEmail === '') {
        return false;
    }

    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->Port = $port > 0 ? $port : 587;
        $mail->SMTPAuth = $smtpAuth;
        if ($smtpAuth) {
            $mail->Username = $username;
            $mail->Password = $password;
        }

        if ($secureMode === 'ssl') {
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($secureMode === 'tls') {
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = '';
            $mail->SMTPAutoTLS = false;
        }

        if ($allowSelfSigned) {
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
        }

        $mail->CharSet = 'UTF-8';
        $mail->setFrom($fromEmail, $fromName !== '' ? $fromName : 'InoSakti');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody));

        return $mail->send();
    } catch (\Throwable $e) {
        return false;
    }
}

function admin_send_email(string $toEmail, string $subject, string $htmlBody): bool
{
    if (admin_smtp_enabled()) {
        return admin_send_email_via_smtp($toEmail, $subject, $htmlBody);
    }

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: InoSakti <no-reply@inosakti.com>',
    ];
    $ok = @mail($toEmail, $subject, $htmlBody, implode("\r\n", $headers));
    if ($ok) {
        return true;
    }
    if (admin_is_local_env()) {
        return true;
    }
    return false;
}

function admin_generate_otp(int $length = 6): string
{
    $max = (10 ** $length) - 1;
    return str_pad((string) random_int(0, $max), $length, '0', STR_PAD_LEFT);
}

function admin_send_otp_email(string $email, string $otp, string $purpose): bool
{
    $purposeLabel = $purpose === 'password_reset'
        ? 'Reset Password'
        : 'Verifikasi Pendaftaran Student';
    $subject = 'Kode OTP InoSakti - ' . $purposeLabel;
    $html = '<div style="font-family:Arial,sans-serif;line-height:1.5;color:#0f172a">'
        . '<h2 style="margin:0 0 8px">InoSakti Account Security</h2>'
        . '<p>Gunakan kode OTP berikut:</p>'
        . '<p style="font-size:28px;font-weight:700;letter-spacing:3px;margin:10px 0">' . admin_e($otp) . '</p>'
        . '<p>Kode berlaku selama 10 menit dan hanya bisa dipakai sekali.</p>'
        . '<p>Jika bukan Anda, abaikan email ini.</p>'
        . '</div>';

    $ok = admin_send_email($email, $subject, $html);
    if ($ok && admin_is_local_env()) {
        $_SESSION['dev_last_otp'] = [
            'email' => $email,
            'purpose' => $purpose,
            'otp' => $otp,
            'generated_at' => date('Y-m-d H:i:s'),
        ];
    }
    return $ok;
}

function admin_create_otp_request(string $email, string $purpose, array $payload = []): array
{
    if (!admin_otp_table_exists()) {
        admin_log_otp_event($email, $purpose, 'request', 'error', 'OTP table is not available.');
        return ['ok' => false, 'message' => 'Fitur OTP belum tersedia. Jalankan migration terbaru.'];
    }
    $email = strtolower(trim($email));
    if ($email === '') {
        admin_log_otp_event($email, $purpose, 'request', 'invalid', 'Email is empty.');
        return ['ok' => false, 'message' => 'Email wajib diisi.'];
    }

    $rateSql = "SELECT COUNT(*) AS cnt
                FROM auth_otp_codes
                WHERE email = ? AND purpose = ? AND created_at >= (NOW() - INTERVAL 10 MINUTE)";
    $rateStmt = admin_db()->prepare($rateSql);
    if (!$rateStmt) {
        admin_log_otp_event($email, $purpose, 'request', 'error', 'Failed preparing OTP rate-limit query.');
        return ['ok' => false, 'message' => 'Gagal menyiapkan OTP.'];
    }
    $rateStmt->bind_param('ss', $email, $purpose);
    $rateStmt->execute();
    $rateRow = $rateStmt->get_result()->fetch_assoc();
    $rateStmt->close();
    if ((int) ($rateRow['cnt'] ?? 0) >= 3) {
        admin_log_otp_event($email, $purpose, 'request', 'rate_limited', 'OTP request blocked by rate limiter.');
        return ['ok' => false, 'message' => 'Terlalu banyak permintaan OTP. Coba lagi beberapa menit lagi.'];
    }

    $otp = admin_generate_otp(6);
    $otpHash = hash('sha256', $otp);
    $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
    if (!is_string($payloadJson)) {
        $payloadJson = '{}';
    }
    $expiresAt = date('Y-m-d H:i:s', time() + ADMIN_OTP_TTL_SECONDS);

    $insertSql = "INSERT INTO auth_otp_codes
                  (email, purpose, otp_hash, payload_json, attempt_count, expires_at, created_at)
                  VALUES (?, ?, ?, ?, 0, ?, NOW())";
    $insertStmt = admin_db()->prepare($insertSql);
    if (!$insertStmt) {
        admin_log_otp_event($email, $purpose, 'request', 'error', 'Failed preparing OTP insert query.');
        return ['ok' => false, 'message' => 'Gagal menyimpan OTP.'];
    }
    $insertStmt->bind_param('sssss', $email, $purpose, $otpHash, $payloadJson, $expiresAt);
    $ok = $insertStmt->execute();
    $requestId = (int) admin_db()->insert_id;
    $insertStmt->close();
    if (!$ok || $requestId <= 0) {
        admin_log_otp_event($email, $purpose, 'request', 'error', 'OTP row creation failed.');
        return ['ok' => false, 'message' => 'Gagal membuat OTP.'];
    }

    if (!admin_send_otp_email($email, $otp, $purpose)) {
        admin_log_otp_event($email, $purpose, 'delivery', 'failed', 'SMTP delivery failed.', $requestId);
        return ['ok' => false, 'message' => 'Gagal mengirim OTP ke email.'];
    }
    admin_log_otp_event($email, $purpose, 'delivery', 'sent', 'OTP delivery accepted by mail sender.', $requestId);

    $result = [
        'ok' => true,
        'message' => 'OTP sudah dikirim ke email Anda.',
        'request_id' => $requestId,
    ];
    if (admin_is_local_env() && !admin_smtp_enabled()) {
        $result['dev_otp'] = $otp;
    }
    return $result;
}

function admin_verify_otp_request(int $requestId, string $email, string $purpose, string $otp): array
{
    if (!admin_otp_table_exists()) {
        admin_log_otp_event($email, $purpose, 'verify', 'error', 'OTP table is not available.', $requestId);
        return ['ok' => false, 'message' => 'Fitur OTP belum tersedia.'];
    }
    if ($requestId <= 0) {
        admin_log_otp_event($email, $purpose, 'verify', 'invalid', 'Request ID is invalid.', $requestId);
        return ['ok' => false, 'message' => 'Request OTP tidak valid.'];
    }

    $email = strtolower(trim($email));
    $otp = trim($otp);
    if ($email === '' || $otp === '') {
        admin_log_otp_event($email, $purpose, 'verify', 'invalid', 'Email or OTP code is empty.', $requestId);
        return ['ok' => false, 'message' => 'Email dan OTP wajib diisi.'];
    }

    $sql = "SELECT id, email, purpose, otp_hash, payload_json, attempt_count, expires_at
            FROM auth_otp_codes
            WHERE id = ? AND email = ? AND purpose = ? AND consumed_at IS NULL
            LIMIT 1";
    $stmt = admin_db()->prepare($sql);
    if (!$stmt) {
        admin_log_otp_event($email, $purpose, 'verify', 'error', 'Failed preparing OTP verify query.', $requestId);
        return ['ok' => false, 'message' => 'Gagal memverifikasi OTP.'];
    }
    $stmt->bind_param('iss', $requestId, $email, $purpose);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        admin_log_otp_event($email, $purpose, 'verify', 'not_found', 'OTP request not found, consumed, or mismatched.', $requestId);
        return ['ok' => false, 'message' => 'OTP tidak ditemukan atau sudah dipakai.'];
    }

    if ((int) ($row['attempt_count'] ?? 0) >= ADMIN_OTP_MAX_ATTEMPTS) {
        admin_log_otp_event($email, $purpose, 'verify', 'blocked', 'OTP max attempts reached.', (int) $row['id']);
        return ['ok' => false, 'message' => 'OTP sudah melebihi batas percobaan.'];
    }

    $expiresAt = strtotime((string) ($row['expires_at'] ?? ''));
    if ($expiresAt === false || $expiresAt < time()) {
        admin_log_otp_event($email, $purpose, 'verify', 'expired', 'OTP code expired.', (int) $row['id']);
        return ['ok' => false, 'message' => 'OTP sudah kedaluwarsa.'];
    }

    $actual = hash('sha256', $otp);
    if (!hash_equals((string) $row['otp_hash'], $actual)) {
        $attemptSql = "UPDATE auth_otp_codes SET attempt_count = attempt_count + 1 WHERE id = ?";
        $attemptStmt = admin_db()->prepare($attemptSql);
        if ($attemptStmt) {
            $id = (int) $row['id'];
            $attemptStmt->bind_param('i', $id);
            $attemptStmt->execute();
            $attemptStmt->close();
        }
        admin_log_otp_event($email, $purpose, 'verify', 'invalid_code', 'OTP code mismatch.', (int) $row['id']);
        return ['ok' => false, 'message' => 'OTP tidak valid.'];
    }

    $touchSql = "UPDATE auth_otp_codes SET verified_at = NOW() WHERE id = ?";
    $touchStmt = admin_db()->prepare($touchSql);
    if ($touchStmt) {
        $id = (int) $row['id'];
        $touchStmt->bind_param('i', $id);
        $touchStmt->execute();
        $touchStmt->close();
    }
    admin_log_otp_event($email, $purpose, 'verify', 'verified', 'OTP code verified successfully.', (int) $row['id']);

    $payload = [];
    $payloadRaw = (string) ($row['payload_json'] ?? '{}');
    $decoded = json_decode($payloadRaw, true);
    if (is_array($decoded)) {
        $payload = $decoded;
    }

    return [
        'ok' => true,
        'message' => 'OTP valid.',
        'request_id' => (int) $row['id'],
        'payload' => $payload,
    ];
}

function admin_consume_otp_request(int $requestId): void
{
    if ($requestId <= 0 || !admin_otp_table_exists()) {
        return;
    }
    $sql = "UPDATE auth_otp_codes SET consumed_at = NOW() WHERE id = ? AND consumed_at IS NULL";
    $stmt = admin_db()->prepare($sql);
    if (!$stmt) {
        return;
    }
    $stmt->bind_param('i', $requestId);
    $stmt->execute();
    $stmt->close();
}

