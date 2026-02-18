<?php
header('Content-Type: application/manifest+json; charset=utf-8');

// Deteksi basePath untuk subfolder /inosakti.com
$req = $_SERVER['REQUEST_URI'] ?? '/';
$base = (preg_match('#^/inosakti\.com(/|$)#', $req)) ? '/inosakti.com' : '';
?>

{
  "id": "<?php echo $base; ?>/",
  "name": "InoSakti",
  "short_name": "InoSakti",
  "start_url": "<?php echo $base; ?>/",
  "scope": "<?php echo $base; ?>/",
  "display": "standalone",
  "background_color": "#0F172A",
  "theme_color": "#1E40AF",
  "icons": [
    {
      "src": "<?php echo $base; ?>/assets/img/favicon-192.png",
      "sizes": "192x192",
      "type": "image/png",
      "purpose": "any"
    },
    {
      "src": "<?php echo $base; ?>/assets/img/favicon-512.png",
      "sizes": "512x512",
      "type": "image/png",
      "purpose": "any"
    }
  ],
  "screenshots": [
    {
      "src": "<?php echo $base; ?>/assets/img/background_utama.jpeg",
      "sizes": "1280x720",
      "type": "image/jpeg",
      "form_factor": "wide"
    },
    {
      "src": "<?php echo $base; ?>/assets/img/produk_1.png",
      "sizes": "720x1280",
      "type": "image/png"
    }
  ]
}
