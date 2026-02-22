<?php
// config.php
// Auto-detect base path for local vs production environments.
// Place this file in inc/ and include it before outputting links.

if (!function_exists('inosakti_load_env')) {
    /**
     * Minimal .env loader (KEY=VALUE) without external dependency.
     */
    function inosakti_load_env(string $envPath): array
    {
        $values = [];
        if (!is_file($envPath) || !is_readable($envPath)) {
            return $values;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }

            $key = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));
            if ($key === '') {
                continue;
            }

            // Strip simple single/double quote wrappers
            if (
                strlen($value) >= 2 &&
                (($value[0] === '"' && $value[strlen($value) - 1] === '"')
                || ($value[0] === "'" && $value[strlen($value) - 1] === "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            $values[$key] = $value;
        }

        return $values;
    }
}

if (!isset($env) || !is_array($env)) {
    // Production (cPanel): /home/<user>/environment/.env
    $homePath = dirname(__DIR__, 2);
    $env = inosakti_load_env($homePath . '/environment/.env');
    if (!$env) {
        // Local development fallback: project root/.env
        $env = inosakti_load_env(dirname(__DIR__) . '/.env');
    }
}

if (!function_exists('inosakti_env_value')) {
    function inosakti_env_value(string $key, ?string $default = null): ?string
    {
        global $env;
        if (isset($env[$key])) {
            return $env[$key];
        }
        $serverValue = $_SERVER[$key] ?? null;
        if ($serverValue !== null && $serverValue !== '') {
            return (string) $serverValue;
        }
        $getEnvValue = getenv($key);
        if ($getEnvValue !== false && $getEnvValue !== '') {
            return (string) $getEnvValue;
        }
        return $default;
    }
}

if (!function_exists('inosakti_app_timezone')) {
    function inosakti_app_timezone(): string
    {
        $tz = inosakti_env_value('APP_TIMEZONE', 'Asia/Jakarta');
        return is_string($tz) && $tz !== '' ? $tz : 'Asia/Jakarta';
    }
}

if (!function_exists('inosakti_db_timezone')) {
    function inosakti_db_timezone(): string
    {
        $tz = inosakti_env_value('DB_TIMEZONE', '+07:00');
        return is_string($tz) && $tz !== '' ? $tz : '+07:00';
    }
}

if (!function_exists('inosakti_set_default_timezone')) {
    function inosakti_set_default_timezone(): void
    {
        date_default_timezone_set(inosakti_app_timezone());
    }
}

if (!function_exists('inosakti_init_db_connection')) {
    function inosakti_init_db_connection(mysqli $db): void
    {
        $db->set_charset('utf8mb4');
        $tz = inosakti_db_timezone();
        $stmt = $db->prepare('SET time_zone = ?');
        if ($stmt) {
            $stmt->bind_param('s', $tz);
            $stmt->execute();
            $stmt->close();
        }
    }
}

inosakti_set_default_timezone();

if (!isset($dbConfig) || !is_array($dbConfig)) {
    $dbConfig = [
        'host' => inosakti_env_value('DB_HOST', 'localhost'),
        'port' => (int) inosakti_env_value('DB_PORT', '3306'),
        'name' => inosakti_env_value('DB_NAME', ''),
        'user' => inosakti_env_value('DB_USER', ''),
        'pass' => inosakti_env_value('DB_PASS', ''),
    ];
}

// allow manual override from pages (set $basePath beforehand)
if (!isset($basePath)) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    // strip any port number from host for easier matching
    $hostNoPort = preg_replace('/:\d+$/', '', $host);

    // only keep path portion, ignore query and avoid accidental filesystem paths
    $uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    // if Apache ever passes a Windows path, discard drive letter and leading backslashes/hinted slashes
    $uri = preg_replace('#^[A-Za-z]:(?:\\|/)*#', '/', $uri);

    // Local dev (xampp biasanya localhost:8080)
    $isLocal = in_array($hostNoPort, ['localhost', '127.0.0.1'])
               || (PHP_VERSION_ID >= 80000 ? str_ends_with($hostNoPort, '.local') : substr($hostNoPort, -6) === '.local');

    // Kalau local dan memang aksesnya di /inosakti.com, pakai subfolder itu
    if ($isLocal && preg_match('#^/inosakti\.com(/|$)#', $uri)) {
        $basePath = '/inosakti.com';
    } else {
        // Production (inosakti.com root) atau local tapi bukan subfolder itu
        $basePath = '';
    }
}
