<?php
// config.php
// Auto-detect base path for local vs production environments.
// Place this file in inc/ and include it before outputting links.

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
