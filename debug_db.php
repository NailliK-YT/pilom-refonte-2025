<?php

// Load the CodeIgniter framework bootstrap
require __DIR__ . '/app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';

exit(CodeIgniter\Boot::bootWeb($paths));

// We can't easily boot the whole app in a single script without using spark or index.php logic.
// Let's try a simpler approach by just loading DotEnv.
