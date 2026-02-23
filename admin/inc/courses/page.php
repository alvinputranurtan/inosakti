<?php
declare(strict_types=1);

require_once __DIR__ . '/../layout.php';
require __DIR__ . '/partials/guard.php';
require __DIR__ . '/partials/actions.php';
require __DIR__ . '/partials/data.php';

admin_render_start('Manajemen Kursus', 'courses');
require __DIR__ . '/partials/view.php';
admin_render_end();
