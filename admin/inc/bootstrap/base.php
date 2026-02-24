<?php
declare(strict_types=1);

function admin_base_path(): string
{
    global $basePath;
    return (isset($basePath) && is_string($basePath)) ? $basePath : '';
}

function admin_url(string $path = '/'): string
{
    $base = rtrim(admin_base_path(), '/');
    $suffix = '/' . ltrim($path, '/');
    if ($base === '') {
        return $suffix;
    }
    return $base . $suffix;
}

function admin_e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

